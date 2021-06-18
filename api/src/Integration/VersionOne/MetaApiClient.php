<?php

namespace App\Integration\VersionOne;

use Http\Message\RequestFactory;
use Psr\Http\Client\ClientInterface;

class MetaApiClient
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
    private $metaEndpoint;

    public function __construct(ClientInterface $client, RequestFactory $requestFactory, string $metaEndpoint)
    {
        $this->httpClient = $client;
        $this->requestFactory = $requestFactory;
        $this->metaEndpoint = $metaEndpoint;
    }

    public function getMetadata(string $asset): array
    {
        // Add the 'accept' GET parameter to solve the issue https://groups.google.com/g/versionone-dev/c/x1Xs0U4d9DE
        $uri = sprintf('%s/%s?accept=application/json', $this->metaEndpoint, $asset);
        $request = $this->requestFactory->createRequest('GET', $uri);
        $response = $this->httpClient->sendRequest($request);
        if ($response->getStatusCode() !== 200) {
            throw new \RuntimeException($response->getReasonPhrase(), $response->getStatusCode());
        }

        $contents = $response->getBody()->getContents();
        $contents = json_decode($contents, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException(json_last_error_msg(), json_last_error());
        }

        return $contents;
    }
}
