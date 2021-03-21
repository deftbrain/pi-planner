<?php

namespace App\Integration\VersionOne\AssetMetadata\Timebox;

use App\Integration\VersionOne\AssetMetadata\BaseAsset\BaseAssetAssetMetadata;

class TimeboxAssetMetadata extends BaseAssetAssetMetadata
{
    public function __construct()
    {
        parent::__construct();
        $this->attributes = array_merge(
            $this->attributes,
            [
                new ScheduleAttribute,
                new BeginDateAttribute,
                new EndDateAttribute,
            ]
        );
    }

    public function getType(): string
    {
        return 'Timebox';
    }
}
