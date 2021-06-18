<?php

namespace App\Integration\VersionOne\AssetMetadata\Theme;

use App\Integration\VersionOne\AssetMetadata\BaseAsset\BaseAssetAssetMetadata;

class ThemeAssetMetadata extends BaseAssetAssetMetadata
{
    public function __construct()
    {
        parent::__construct();
        $this->attributes = array_merge(
            $this->attributes,
            [
                new OrderAttribute,
                new ScopeChildrenMeAndDownAttribute,
            ]
        );
    }

    public function getType(): string
    {
        return 'Theme';
    }
}
