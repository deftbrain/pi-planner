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
     * @param string $assetMetadataClassName
     */
    public function find(string $assetMetadataClassName): array
    {
        return $this->findAssets(
            $assetMetadataClassName,
            [AssetMetadata\Asset::ATTRIBUTE_STATE => AssetMetadata\Asset::ATTRIBUTE_STATE_ACTIVE]
        );
    }

    /**
     * @param string|AssetMetadata\Asset $assetMetadataClassName
     * @param array $filter
     * @return array
     */
    private function findAssets(string $assetMetadataClassName, array $filter = []): array
    {
        /** @var  $assetMetadataClassName */
        $query = $this->makeQueryBuilder()
            ->from($assetMetadataClassName::getType())
            ->select($assetMetadataClassName::getAttributesToSelect())
            ->filter($filter)
            ->getQuery();

        return $this->sendQuery($query);
    }

    private function makeQueryBuilder(): QueryBuilder
    {
        return new QueryBuilder;
    }

    private function sendQuery(array $query): array
    {
        $body = json_encode($query);
        $request = $this->requestFactory->createRequest('POST', '', [], $body);
        $response = $this->httpClient->sendRequest($request);
        $contents = $response->getBody()->getContents();
        $contents = json_decode($contents, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException(json_last_error_msg(), json_last_error());
        }

        if ($response->getStatusCode() !== 200) {
            throw new \RuntimeException($contents['Exceptions'][0], $response->getStatusCode());
        }

        if ($contents['commandFailures']['count']) {
            throw new \RuntimeException(json_encode($contents['commandFailures']['commands']));
        }

        return $contents['queryResult']['results'][0];
    }
}
