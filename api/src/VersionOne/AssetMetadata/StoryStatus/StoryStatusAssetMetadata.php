<?php

namespace App\VersionOne\AssetMetadata\StoryStatus;

use App\VersionOne\AssetMetadata\BaseAsset\BaseAssetAssetMetadata;

class StoryStatusAssetMetadata extends BaseAssetAssetMetadata
{
    public function __construct()
    {
        parent::__construct();
        $this->attributes = array_merge(
            $this->attributes,
            [
                new OrderAttribute,
            ]
        );
    }

    public function getType(): string
    {
        return 'StoryStatus';
    }
}
