<?php

namespace App\VersionOne\AssetMetadata\Scope;

use App\VersionOne\AssetMetadata\BaseAsset\BaseAssetAssetMetadata;

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
