<?php

namespace App\VersionOne;

use Http\Message\RequestFactory;
use Psr\Http\Client\ClientInterface;

class BulkApiClient
{
    /**
     * @var ClientInterface
     */
    private $httpClient;

    /**
     * @var RequestFactory
     */
    private $requestFactory;

    /**
     * @var string
     */
    private $bulkEndpoint;

    public function __construct(ClientInterface $client, RequestFactory $requestFactory, string $bulkEndpoint)
    {
        $this->httpClient = $client;
        $this->requestFactory = $requestFactory;
        $this->bulkEndpoint = $bulkEndpoint;
    }

    /**
     * @return array
     */
    public function find(...$queries): array
    {
        return $this->sendQuery($queries)['queryResult']['results'];
    }

    public function createAsset(string $assetType, array $values): string
    {
        $content = $this->sendQuery(['AssetType' => $assetType] + $values);
        if (!empty($content['assetsCreated']['oidTokens'])) {
            return $content['assetsCreated']['oidTokens'][0];
        }

        throw new \RuntimeException('Unable to retrieve the Oid token from the response body');
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
        $request = $this->requestFactory->createRequest('POST', $this->bulkEndpoint, [], $body);
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
