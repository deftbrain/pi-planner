<?php

namespace App\Integration\VersionOne\AssetMetadata\Scope;

use App\Integration\VersionOne\AssetMetadata\BaseAsset\BaseAssetAssetMetadata;

class ScopeAssetMetadata extends BaseAssetAssetMetadata
{
    public function __construct()
    {
        parent::__construct();
        $this->attributes = array_merge(
            $this->attributes,
            [
                new ScheduleAttribute,
            ]
        );
    }

    public function getType(): string
    {
        return 'Scope';
    }
}
