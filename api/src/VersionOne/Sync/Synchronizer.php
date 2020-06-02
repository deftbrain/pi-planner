<?php

namespace App\VersionOne\Sync;

use App\Entity\ProgramIncrement;
use App\VersionOne\ApiClient;
use App\VersionOne\AssetMetadata\Asset;
use App\VersionOne\AssetMetadata\Epic;
use App\VersionOne\AssetMetadata\Workitem;
use App\VersionOne\Sync\FilterProvider\EpicFilterProvider;
use App\VersionOne\Sync\FilterProvider\WorkitemFilterProvider;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Serializer\SerializerInterface;

class Synchronizer
{
    private const FILTER_PROVIDER = [
        Epic::class => EpicFilterProvider::class,
        Workitem::class => WorkitemFilterProvider::class,
    ];

    private const PI_DEPENDENT_ASSETS = [
        Epic::class,
        Workitem::class,
    ];

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

    /**
     * @var ParameterBagInterface
     */
    private $params;

    public function __construct(
        ApiClient $v1ApiClient,
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer,
        ParameterBagInterface $params
    ) {
        $this->versionOneApiClient = $v1ApiClient;
        $this->entityManager = $entityManager;
        $this->serializer = $serializer;
        $this->params = $params;
    }

    /**
     * @param string|Asset $assetClassName
     */
    public function syncAssets(string $assetClassName): void
    {
        if (in_array($assetClassName, $this->assetsToSync, true)) {
            return;
        }
        $this->assetsToSync[] = $assetClassName;

        if (
            in_array($assetClassName, self::PI_DEPENDENT_ASSETS, true)
            && !$this->entityManager->getRepository(ProgramIncrement::class)->count([])
        ) {
            printf(
                'At least one program increment should be created to import affected %ss' . PHP_EOL,
                strtolower($assetClassName::getType())
            );
            return;
        }

        $this->syncAssetDependencies($assetClassName);

        $filterProviderClassName = self::FILTER_PROVIDER[$assetClassName] ?? null;
        $filter = $filterProviderClassName
            ? (new $filterProviderClassName($this->entityManager, $this->params))->getFilter()
            : [];
        $entityClassName = AssetToEntityMap::MAP[$assetClassName];
        $assets = $this->versionOneApiClient->find($assetClassName, $filter);
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