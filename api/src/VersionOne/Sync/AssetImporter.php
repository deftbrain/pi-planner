<?php

namespace App\VersionOne\Sync;

use App\VersionOne\ApiClient;
use App\VersionOne\AssetMetadata\Asset;
use App\VersionOne\AssetMetadata\Epic;
use App\VersionOne\AssetMetadata\Workitem;
use App\VersionOne\Sync\FilterProvider\EpicFilterProvider;
use App\VersionOne\Sync\FilterProvider\FilterProviderInterface;
use App\VersionOne\Sync\FilterProvider\WorkitemFilterProvider;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Serializer\SerializerInterface;

class AssetImporter
{
    private const FILTER_PROVIDER = [
        Epic::class => EpicFilterProvider::class,
        Workitem::class => WorkitemFilterProvider::class,
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

    /**
     * @var ParameterBagInterface
     */
    private $params;

    /**
     * @var RouterInterface
     */
    private $router;

    private $assetTypesToImport = [];

    public function __construct(
        ApiClient $v1ApiClient,
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer,
        ParameterBagInterface $params,
        RouterInterface $router
    ) {
        $this->versionOneApiClient = $v1ApiClient;
        $this->entityManager = $entityManager;
        $this->serializer = $serializer;
        $this->params = $params;
        $this->router = $router;
    }

    /**
     * @param string|Asset $assetClassName
     */
    public function importAssets(string $assetClassName): void
    {
        if (in_array($assetClassName, $this->assetTypesToImport, true)) {
            return;
        }
        $this->assetTypesToImport[] = $assetClassName;

        $this->importAssetDependencies($assetClassName);

        /** @var FilterProviderInterface|null $filterProviderClassName */
        $filterProviderClassName = self::FILTER_PROVIDER[$assetClassName] ?? null;
        if ($filterProviderClassName) {
            $filter = (new $filterProviderClassName($this->entityManager, $this->params, $this->router))->getFilter();
            if (!$filter) {
                return;
            }
        } else {
            $filter = [];
        }
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

    private function importAssetDependencies($assetClassName): void
    {
        $dependencies = array_keys(
            array_intersect_key(
                $assetClassName::getAttributesToSelect(),
                AssetToEntityMap::MAP
            )
        );
        foreach ($dependencies as $dependencyClassName) {
            $this->importAssets($dependencyClassName);
        }
    }
}
