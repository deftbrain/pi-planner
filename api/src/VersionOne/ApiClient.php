<?php

namespace App\VersionOne;

use Http\Message\RequestFactory;
use Psr\Http\Client\ClientInterface;

class ApiClient
{
    /**
     * @var ClientInterface
     */
    private $httpClient;

    /**
     * @var RequestFactory
     */
    private $requestFactory;

    public function __construct(ClientInterface $client, RequestFactory $requestFactory)
    {
        $this->httpClient = $client;
        $this->requestFactory = $requestFactory;
    }

    /**
     * @return array
     */
    public function find(...$queries): array
    {
        return $this->sendQuery($queries)['queryResult']['results'];
    }

    public function updateAsset(string $oid, array $values): void
    {
        $query = $this->makeQueryBuilder()
            ->from($oid)
            ->update($values)
            ->getQuery();

        $this->sendQuery($query);
    }

    public function makeQueryBuilder(): QueryBuilder
    {
        return new QueryBuilder;
    }

    private function sendQuery(array $query): array
    {
        $body = json_encode($query);
        $request = $this->requestFactory->createRequest('POST', '', [], $body);
        $response = $this->httpClient->sendRequest($request);
        if ($response->getStatusCode() !== 200) {
            throw new \RuntimeException($response->getReasonPhrase(), $response->getStatusCode());
        }

        $contents = $response->getBody()->getContents();
        $contents = json_decode($contents, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException(json_last_error_msg(), json_last_error());
        }

        if ($contents['commandFailures']['count']) {
            throw new \RuntimeException(json_encode($contents['commandFailures']['commands']));
        }

        return $contents;
    }
}
