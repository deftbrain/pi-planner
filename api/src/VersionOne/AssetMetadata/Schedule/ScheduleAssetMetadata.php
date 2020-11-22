<?php

namespace App\VersionOne\AssetMetadata\Schedule;

use App\VersionOne\AssetMetadata\BaseAsset\BaseAssetAssetMetadata;

class ScheduleAssetMetadata extends BaseAssetAssetMetadata
{
    public function getType(): string
    {
        return 'Schedule';
    }
}
