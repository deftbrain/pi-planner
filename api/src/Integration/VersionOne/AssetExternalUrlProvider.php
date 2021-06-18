<?php

namespace App\Integration\VersionOne;

use App\Integration\AssetExternalUrlProviderInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class AssetExternalUrlProvider implements AssetExternalUrlProviderInterface
{
    private ParameterBagInterface $params;

    public function __construct(ParameterBagInterface $params)
    {
        $this->params = $params;
    }

    public function getUrl(string $externalId): string
    {
        return sprintf(
            '%s%s?%s',
            $this->params->get('version_one.server_base_uri'),
            $this->params->get('version_one.endpoint.asset_detail'),
            http_build_query(['oid' => $externalId])
        );
    }
}
