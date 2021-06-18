<?php

namespace App\Integration\VersionOne\AssetMetadata\Schedule;

use App\Integration\VersionOne\AssetMetadata\BaseAsset\BaseAssetAssetMetadata;

class ScheduleAssetMetadata extends BaseAssetAssetMetadata
{
    public function getType(): string
    {
        return 'Schedule';
    }
}
