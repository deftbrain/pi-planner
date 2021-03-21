<?php

namespace App\Integration\VersionOne\AssetMetadata\EpicStatus;

use App\Integration\VersionOne\AssetMetadata\BaseAsset\BaseAssetAssetMetadata;

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
