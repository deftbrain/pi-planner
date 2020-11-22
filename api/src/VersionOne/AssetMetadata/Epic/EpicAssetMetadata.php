<?php

namespace App\VersionOne\AssetMetadata\Epic;

use App\VersionOne\AssetMetadata\BaseAsset\BaseAssetAssetMetadata;

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
            ]
        );
    }

    public function getType(): string
    {
        return 'Epic';
    }
}
