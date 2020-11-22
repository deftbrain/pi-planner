<?php

namespace App\VersionOne\Sync\AssetImporter;

use App\VersionOne\AssetMetadata\AssetMetadataInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class AssetImporterFactory
{
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function makeImporter(AssetMetadataInterface $assetMetadata): AssetImporter
    {
        $className = sprintf('%s\\%sAssetImporter', __NAMESPACE__, $assetMetadata->getType());
        /** @var AssetImporter $assetImporter */
        $assetImporter = $this->container->has($className)
            ? $this->container->get($className)
            : $this->container->get(AssetImporter::class);
        $assetImporter->setAssetMetadata($assetMetadata);
        return $assetImporter;
    }
}
