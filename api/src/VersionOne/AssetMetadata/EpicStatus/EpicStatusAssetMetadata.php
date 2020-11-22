<?php

namespace App\VersionOne\AssetMetadata\EpicStatus;

use App\VersionOne\AssetMetadata\BaseAsset\BaseAssetAssetMetadata;

class EpicStatusAssetMetadata extends BaseAssetAssetMetadata
{
    public function __construct()
    {
        parent::__construct();
        $this->attributes = array_merge(
            $this->attributes,
            [
                new OrderAttribute,
            ]
        );
    }

    public function getType(): string
    {
        return 'EpicStatus';
    }
}
