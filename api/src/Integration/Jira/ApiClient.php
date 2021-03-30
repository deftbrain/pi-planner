<?php

namespace App\Integration\Jira;

use Http\Message\RequestFactory;
use Psr\Http\Client\ClientInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Response;

class ApiClient
{
    private ClientInterface $httpClient;
    private RequestFactory $requestFactory;
    private ParameterBagInterface $params;

    public function __construct(ClientInterface $client, RequestFactory $requestFactory, ParameterBagInterface $params)
    {
        $this->httpClient = $client;
        $this->requestFactory = $requestFactory;
        $this->params = $params;
    }

    public function getProjects(array $projectKeys): array
    {
        return array_filter(
            $this->sendRequest($this->params->get('jira.endpoint.project')),
            static fn ($project) => in_array($project['key'], $projectKeys, true)
        );
    }

    public function getSprints(string $boardId, ?int $startAt): array
    {
        $queryString = http_build_query(['startAt' => $startAt]);
        return $this->sendRequest(
            sprintf('%s/%s/sprint?%s', $this->params->get('jira.endpoint.board'), $boardId, $queryString)
        );
    }

    public function processPaginatedData(callable $responseProvider, callable $responseProcessor)
    {
        $startAt = 0;
        do {
            $response = $responseProvider($this, $startAt);
            $responseProcessor($response, $startAt);
            $startAt += $response['maxResults'];
            // Don't use the isLast flag only because it doesn't appear in some cases e.g. during getting epics
        } while (empty($response['isLast']) && (!empty($response['issues']) || !empty($response['values'])));
    }

    public function getBoards(string $projectKey, ?int $startAt): array
    {
        $queryString = http_build_query(['projectKeyOrId' => $projectKey, 'type' => 'scrum', 'startAt' => $startAt]);
        $response = $this->sendRequest(sprintf('%s?%s', $this->params->get('jira.endpoint.board'), $queryString));
        return $response;
    }

    public function getEpics(array $projectKeys, int $startAt): array
    {
        if (!$projectKeys) {
            return [];
        }

        $body = [
            'jql' => sprintf(
                'project IN (%s) AND issuetype = Epic AND statusCategory != Done ORDER BY Rank',
                implode(',', $projectKeys)
            ),
            'fields' => [
                'summary',
                'description',
                'project',
                'updated',
                $this->params->get('jira.custom_field.program_increment'),
            ],
            // To get description in the HTML format
            'expand' => ['renderedFields'],
            'startAt' => $startAt,
        ];
        return $this->sendRequest($this->params->get('jira.endpoint.search'), 'POST', $body);
    }

    public function getIssues(array $epicKeys, array $issueTypes, int $startAt): array
    {
        if (!$epicKeys || !$issueTypes) {
            return [];
        }

        $body = [
            'jql' => sprintf(
                'cf[%s] IN (%s) AND issuetype IN (\'%s\')',
                explode('_', $this->params->get('jira.custom_field.epic'))[1],
                implode(',', $epicKeys),
                implode('\',\'', $issueTypes),
            ),
            'fields' => [
                'summary',
                'project',
                'updated',
                $this->params->get('jira.custom_field.epic'),
                $this->params->get('jira.custom_field.team'),
                $this->params->get('jira.custom_field.sprint'),
            ],
            'startAt' => $startAt,
        ];
        return $this->sendRequest($this->params->get('jira.endpoint.search'), 'POST', $body);
    }

    public function getIssueFieldValues(array $projectKeys, string $issueTypeName, string $fieldKey): array
    {
        $url = sprintf('%s?%s',
            $this->params->get('jira.endpoint.issue_createmeta'),
            http_build_query([
                'projectKeys' => implode(',', $projectKeys),
                'issuetypeNames' => $issueTypeName,
                'expand' => 'projects.issuetypes.fields',
            ])
        );
        $values = [];
        $data = $this->sendRequest($url);
        foreach ($data['projects'] as $project) {
            $projectKey = $project['key'];
            foreach ($project['issuetypes'] as $issueType) {
                foreach ($issueType['fields'] as $field) {
                    if ($field['key'] === $fieldKey) {
                        foreach ($field['allowedValues'] as $value) {
                            $valueId = $value['id'];
                            if (!isset($values[$valueId])) {
                                $values[$valueId] = $value;
                            }
                            // Currently is needed for a BacklogGroup entity only
                            $values[$valueId]['projects'][] = $projectKey;
                        }
                    }
                }
            }
        }

        return $values;
    }

    public function updateIssue(string $externalId, array $values): void
    {
        $url = sprintf('%s/%s', $this->params->get('jira.endpoint.issue'), $externalId);
        $this->sendRequest($url, 'PUT', ['fields' => $values]);
    }

    public function createIssue(array $values): string
    {
        $response = $this->sendRequest($this->params->get('jira.endpoint.issue'), 'POST', ['fields' => $values]);
        return $response['key'];
    }

    private function sendRequest(string $url, string $method = 'GET', $body = null): ?array
    {
        $request = $this->requestFactory->createRequest($method, $url, [], json_encode($body));
        $response = $this->httpClient->sendRequest($request);
        $statusCode = $response->getStatusCode();
        if ($statusCode === Response::HTTP_NO_CONTENT) {
            return null;
        }

        $contents = $response->getBody()->getContents();
        if (!in_array($statusCode, [Response::HTTP_OK, Response::HTTP_CREATED])) {
            throw new \RuntimeException($contents ?: $response->getReasonPhrase(), $statusCode);
        }

        $contents = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
        return $contents;
    }
}
