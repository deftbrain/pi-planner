<?php

namespace App\VersionOne\AssetMetadata\BaseAsset;

use App\VersionOne\AssetMetadata\AbstractAssetMetadata;

class BaseAssetAssetMetadata extends AbstractAssetMetadata
{
    public function __construct()
    {
        $this->attributes = array_merge(
            $this->attributes,
            [
                new IDAttribute,
                new NameAttribute,
                new ChangeDateUTCAttribute,
                new IsDeletedAttribute,
            ]
        );
    }

    public function getType(): string
    {
        return 'BaseAsset';
    }
}
