<?php

namespace App\VersionOne\AssetMetadata;

class AssetMetadataFactory
{
    /**
     * @param AssetMetadataInterface[] $assetTypes
     */
    public function getMetadataFor(array $assetTypes): array
    {
        return array_map(
            function (string $assetType) {
                $className = sprintf('%1$s\\%2$s\\%2$sAssetMetadata', __NAMESPACE__, $assetType);
                return new $className;
            },
            $assetTypes
        );
    }
}
