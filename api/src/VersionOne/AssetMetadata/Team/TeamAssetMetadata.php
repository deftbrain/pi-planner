<?php

namespace App\VersionOne\AssetMetadata\Team;

use App\VersionOne\AssetMetadata\BaseAsset\BaseAssetAssetMetadata;

class TeamAssetMetadata extends BaseAssetAssetMetadata
{

    public function getType(): string
    {
        return 'Team';
    }
}
