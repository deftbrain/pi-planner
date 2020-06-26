<?php

namespace App\VersionOne\Sync;

use App\Entity\AbstractEntity;
use App\VersionOne\ApiClient;
use App\VersionOne\AssetMetadata;
use App\VersionOne\AssetMetadata\Asset;
use App\VersionOne\AssetMetadata\Epic;
use App\VersionOne\AssetMetadata\Workitem;
use App\VersionOne\Sync\FilterProvider\EpicFilterProvider;
use App\VersionOne\Sync\FilterProvider\FilterProviderInterface;
use App\VersionOne\Sync\FilterProvider\WorkitemFilterProvider;
use App\VersionOne\Sync\Serializer\Normalizer;
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
        /** @var FilterProviderInterface|null $filterProviderClassName */
        $filterProviderClassName = self::FILTER_PROVIDER[$assetClassName] ?? null;
        if ($filterProviderClassName) {
            $assetSpecificFilter = (new $filterProviderClassName(
                $this->entityManager, $this->params, $this->router
            ))->getFilter();
            if (!$assetSpecificFilter) {
                // If a filter is not given - required data doesn't exist yet
                return;
            }
        } else {
            $assetSpecificFilter = [];
        }
        $entityClassName = AssetToEntityMap::MAP[$assetClassName];
        $assetType = $assetClassName::getType();
        $relevantAssetsQuery = $this->versionOneApiClient
            ->makeQueryBuilder()
            ->from($assetType)
            ->select($assetClassName::getAttributesToSelect())
            ->filter($assetSpecificFilter)
            ->getQuery();
        $deletedAssetsQuery = $this->versionOneApiClient
            ->makeQueryBuilder()
            ->from($assetType)
            ->filter(array_merge([AssetMetadata\Asset::ATTRIBUTE_IS_DELETED => true], $assetSpecificFilter))
            ->getQuery();
        [$relevantAssets, $deletedAssets] = $this->versionOneApiClient->find($relevantAssetsQuery, $deletedAssetsQuery);
        foreach ($relevantAssets as $asset) {
            try {
                $entity = $this->serializer->denormalize($asset, $entityClassName, Normalizer::FORMAT_V1_JSON);
                $this->entityManager->persist($entity);
            } catch (\DomainException $exception) {
                echo $exception->getMessage() . PHP_EOL;
            }
        }

        $externalIdsToRemove = array_column($deletedAssets, Asset::ATTRIBUTE_ID);
        if ($externalIdsToRemove) {
            $entitiesToMarkAsDeleted = $this->entityManager
                ->getRepository($entityClassName)
                ->findBy(['externalId' => $externalIdsToRemove]);
            $deletedEntitiesCount = 0;
            /** @var AbstractEntity $entity */
            foreach ($entitiesToMarkAsDeleted as $entity) {
                if (!$entity->getIsDeleted()) {
                    // Mark entities as deleted via objects to let entity listeners know
                    // about changes and send updates to the client app automatically via Mercure
                    $entity->setIsDeleted(true);
                    ++$deletedEntitiesCount;
                }
            }

            if ($deletedEntitiesCount) {
                echo "Number of assets marked as entitiesToMarkAsDeleted: $deletedEntitiesCount" . PHP_EOL;
            }
        }

        $this->entityManager->flush();
    }
}
