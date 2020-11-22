<?php

namespace App\VersionOne\AssetMetadata\Theme;

use App\VersionOne\AssetMetadata\BaseAsset\BaseAssetAssetMetadata;

class ThemeAssetMetadata extends BaseAssetAssetMetadata
{
    public function __construct()
    {
        parent::__construct();
        $this->attributes = array_merge(
            $this->attributes,
            [
                new ScopeAttribute,
            ]
        );
    }

    public function getType(): string
    {
        return 'Theme';
    }
}
