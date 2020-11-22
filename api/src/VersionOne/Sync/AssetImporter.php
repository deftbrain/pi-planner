<?php

namespace App\VersionOne\Sync;

use App\Entity\AbstractEntity;
use App\VersionOne\AssetMetadata\AbstractAssetMetadata;
use App\VersionOne\AssetMetadata\AssetMetadataInterface;
use App\VersionOne\AssetMetadata\BaseAsset\IsDeletedAttribute;
use App\VersionOne\AssetMetadata\Epic\EpicAssetMetadata;
use App\VersionOne\AssetMetadata\PrimaryWorkitem\PrimaryWorkitemAssetMetadata;
use App\VersionOne\BulkApiClient;
use App\VersionOne\Sync\FilterProvider\EpicFilterProvider;
use App\VersionOne\Sync\FilterProvider\FilterProviderInterface;
use App\VersionOne\Sync\FilterProvider\WorkitemFilterProvider;
use App\VersionOne\Sync\Serializer\Normalizer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactoryInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class AssetImporter
{
    private const FILTER_PROVIDER = [
        EpicAssetMetadata::class => EpicFilterProvider::class,
        PrimaryWorkitemAssetMetadata::class => WorkitemFilterProvider::class,
    ];

    /**
     * @var BulkApiClient
     */
    private $apiClient;

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

    private ClassMetadataFactoryInterface $classMetadataFactory;

    public function __construct(
        BulkApiClient $apiClient,
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer,
        ParameterBagInterface $params,
        RouterInterface $router,
        ClassMetadataFactoryInterface $classMetadataFactory
    ) {
        $this->apiClient = $apiClient;
        $this->entityManager = $entityManager;
        $this->serializer = $serializer;
        $this->params = $params;
        $this->router = $router;
        $this->classMetadataFactory = $classMetadataFactory;
    }

    public function importAssets(AssetMetadataInterface $assetMetadata): void
    {
        /** @var FilterProviderInterface|null $filterProviderClassName */
        $filterProviderClassName = self::FILTER_PROVIDER[get_class($assetMetadata)] ?? null;
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
        $assetType = $assetMetadata->getType();
        $attributes = array_map(fn($a) => $a->getName(), $assetMetadata->getAttributes());
        $relevantAssetsQuery = $this->apiClient
            ->makeQueryBuilder()
            ->from($assetType)
            ->select($attributes)
            ->filter($assetSpecificFilter)
            ->getQuery();
        $deletedAssetsQuery = $this->apiClient
            ->makeQueryBuilder()
            ->from($assetType)
            ->filter(array_merge([IsDeletedAttribute::getName() => true], $assetSpecificFilter))
            ->getQuery();
        [$relevantAssets, $deletedAssets] = $this->apiClient->find($relevantAssetsQuery, $deletedAssetsQuery);
        foreach ($relevantAssets as $asset) {
            try {
                $entity = $this->serializer->denormalize(
                    $asset,
                    $this->getEntityClass($assetMetadata->getType()),
                    Normalizer::FORMAT_V1_JSON,
                    [AbstractNormalizer::GROUPS => ['readable']]
                );
                $this->entityManager->persist($entity);
            } catch (\DomainException $exception) {
                echo $exception->getMessage() . PHP_EOL;
            }
        }

        $externalIdsToRemove = array_column($deletedAssets, AbstractAssetMetadata::FIELD_OID);
        if ($externalIdsToRemove) {
            $entityClassName = $this->getEntityClass($assetType);
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

    private function getEntityClass(string $assetType): string
    {
        return $this->classMetadataFactory
            ->getMetadataFor(AbstractEntity::class)
            ->getClassDiscriminatorMapping()
            ->getClassForType($assetType);
    }
}
