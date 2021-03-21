<?php

namespace App\Integration\VersionOne\AssetMetadata\Team;

use App\Integration\VersionOne\AssetMetadata\BaseAsset\BaseAssetAssetMetadata;

class TeamAssetMetadata extends BaseAssetAssetMetadata
{

    public function getType(): string
    {
        return 'Team';
    }
}
