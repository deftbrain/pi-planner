<?php

namespace App\VersionOne\Sync;

use App\VersionOne\ApiClient;
use App\VersionOne\AssetMetadata\Asset;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\SerializerInterface;

class Synchronizer
{
    /**
     * @var ApiClient
     */
    private $versionOneApiClient;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    private $assetsToSync = [];

    public function __construct(
        ApiClient $v1ApiClient,
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer
    ) {
        $this->versionOneApiClient = $v1ApiClient;
        $this->entityManager = $entityManager;
        $this->serializer = $serializer;
    }

    /**
     * @param string $assetClassName
     */
    public function syncAssets(string $assetClassName): void
    {
        if (in_array($assetClassName, $this->assetsToSync, true)) {
            return;
        }
        $this->assetsToSync[] = $assetClassName;

        $this->syncAssetDependencies($assetClassName);

        $entityClassName = AssetToEntityMap::MAP[$assetClassName];
        $assets = $this->versionOneApiClient->find($assetClassName);
        foreach ($assets as $asset) {
            try {
                echo $asset[Asset::ATTRIBUTE_ID] . PHP_EOL;
                $entity = $this->serializer->denormalize($asset, $entityClassName);
                $this->entityManager->persist($entity);
            } catch (\DomainException $exception) {
                echo $exception->getMessage() . PHP_EOL;
            }
        }

        $this->entityManager->flush();
    }

    private function syncAssetDependencies($assetClassName): void
    {
        $dependencies = array_keys(
            array_intersect_key(
                $assetClassName::getAttributesToSelect(),
                AssetToEntityMap::MAP
            )
        );
        foreach ($dependencies as $dependencyClassName) {
            $this->syncAssets($dependencyClassName);
        }
    }
}
