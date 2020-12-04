<?php

namespace App\VersionOne\AssetMetadata;

class AssetMetadataFactory
{
    public function makeMetadataFor(string $assetType): AssetMetadataInterface
    {
        $className = sprintf('%1$s\\%2$s\\%2$sAssetMetadata', __NAMESPACE__, $assetType);
        return new $className;
    }
}
