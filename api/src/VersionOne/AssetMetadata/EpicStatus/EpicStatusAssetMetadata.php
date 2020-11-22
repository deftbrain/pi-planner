<?php

namespace App\VersionOne\AssetMetadata\EpicStatus;

use App\VersionOne\AssetMetadata\BaseAsset\BaseAssetAssetMetadata;

class EpicStatusAssetMetadata extends BaseAssetAssetMetadata
{
    public function getType(): string
    {
        return 'EpicStatus';
    }
}
