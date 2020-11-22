<?php

namespace App\VersionOne\AssetMetadata\PrimaryWorkitem;

use App\VersionOne\AssetMetadata\BaseAsset\BaseAssetAssetMetadata;

class PrimaryWorkitemAssetMetadata extends BaseAssetAssetMetadata
{
    public function __construct()
    {
        parent::__construct();
        $this->attributes = array_merge(
            $this->attributes,
            [
                new ParentAttribute,
                new SuperAttribute,
                new ScopeAttribute,
                new TimeboxAttribute,
                new TeamAttribute,
            ]
        );
    }

    public function getType(): string
    {
        return 'PrimaryWorkitem';
    }
}
