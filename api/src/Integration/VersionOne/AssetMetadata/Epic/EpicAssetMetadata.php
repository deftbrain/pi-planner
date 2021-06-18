<?php

namespace App\Integration\VersionOne\AssetMetadata\Epic;

use App\Integration\VersionOne\AssetMetadata\BaseAsset\BaseAssetAssetMetadata;

class EpicAssetMetadata extends BaseAssetAssetMetadata
{
    public function __construct()
    {
        parent::__construct();
        $this->attributes = array_merge(
            $this->attributes,
            [
                new ScopeAttribute,
                new StatusAttribute,
                new WsjfAttribute,
                new DescriptionAttribute,
                new OrderAttribute,
            ]
        );
    }

    public function getType(): string
    {
        return 'Epic';
    }
}
