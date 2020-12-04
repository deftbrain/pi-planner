<?php

namespace App\VersionOne\Sync\AssetImporter;

use App\Entity\AbstractEntity;
use App\VersionOne\AssetMetadata\AbstractAssetMetadata;
use App\VersionOne\AssetMetadata\AssetMetadataInterface;
use App\VersionOne\AssetMetadata\AttributeInterface;
use App\VersionOne\AssetMetadata\BaseAsset\IDAttribute;
use App\VersionOne\AssetMetadata\BaseAsset\IsDeletedAttribute;
use App\VersionOne\BulkApiClient;
use App\VersionOne\Message\SetSameTypeRelationsMessage;
use App\VersionOne\Sync\Serializer\Normalizer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactoryInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class AssetImporter
{
    private BulkApiClient $apiClient;
    protected EntityManagerInterface $entityManager;
    private SerializerInterface $serializer;
    private ClassMetadataFactoryInterface $classMetadataFactory;
    private AssetMetadataInterface $assetMetadata;
    private MessageBusInterface $messageBus;

    public function __construct(
        BulkApiClient $apiClient,
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer,
        ClassMetadataFactoryInterface $classMetadataFactory,
        MessageBusInterface $messageBus
    ) {
        $this->apiClient = $apiClient;
        $this->entityManager = $entityManager;
        $this->serializer = $serializer;
        $this->classMetadataFactory = $classMetadataFactory;
        $this->messageBus = $messageBus;
    }

    public function setAssetMetadata(AssetMetadataInterface $assetMetadata): void
    {
        $this->assetMetadata = $assetMetadata;
    }

    public function import(): void
    {
        $this->importAssets([]);
    }

    protected function importAssets(array $filter): void
    {
        $assetType = $this->assetMetadata->getType();
        /** @var AttributeInterface[] $attributes */
        $attributes = array_map(fn($a) => $a->getName(), $this->assetMetadata->getAttributes());
        $relevantAssetsQuery = $this->apiClient
            ->makeQueryBuilder()
            ->from($assetType)
            ->select($attributes)
            ->filter($filter)
            ->getQuery();
        $deletedAssetsQuery = $this->apiClient
            ->makeQueryBuilder()
            ->from($assetType)
            ->filter(array_merge([IsDeletedAttribute::getName() => true], $filter))
            ->getQuery();
        [$relevantAssets, $deletedAssets] = $this->apiClient->find($relevantAssetsQuery, $deletedAssetsQuery);
        $entityClassName = $this->getEntityClass($assetType);
        $this->persistAssets($relevantAssets, $entityClassName);
        $this->markRelatedEntitiesAsDeleted($deletedAssets, $entityClassName);
    }

    private function getEntityClass(string $assetType): string
    {
        return $this->classMetadataFactory
            ->getMetadataFor(AbstractEntity::class)
            ->getClassDiscriminatorMapping()
            ->getClassForType($assetType);
    }

    private function persistAssets(array $assets, string $entityClassName): void
    {
        foreach ($assets as $asset) {
            $asset = $this->delaySettingSameTypeRelations($asset, $entityClassName);
            $this->persistAsset($asset, $entityClassName);
        }
    }

    private function delaySettingSameTypeRelations(array $asset, string $entityClassName): array
    {
        $assetType = $this->assetMetadata->getType();
        $sameTypeRelations = array_filter(
            $this->assetMetadata->getAttributes(),
            fn($a) => $a->isRelation() && $a->getRelatedAsset() === $assetType
        );
        if (!$sameTypeRelations) {
            return $asset;
        }
        $sameTypeRelationAttributes = array_map(fn($a) => $a::getName(), $sameTypeRelations);
        $this->messageBus->dispatch(
            new SetSameTypeRelationsMessage(
                array_intersect_key($asset, array_flip($sameTypeRelationAttributes + [IDAttribute::getName()])),
                $entityClassName
            )
        );
        return array_diff_key($asset, array_flip($sameTypeRelationAttributes));
    }

    public function persistAsset(array $asset, string $entityClassName): void
    {
        try {
            $entity = $this->serializer->denormalize(
                $asset,
                $entityClassName,
                Normalizer::FORMAT_V1_JSON,
                [
                    ObjectNormalizer::DISABLE_TYPE_ENFORCEMENT => true,
                    ObjectNormalizer::GROUPS => ['readable'],
                ]
            );
            $this->entityManager->persist($entity);
            $this->entityManager->flush();
        } catch (\DomainException $exception) {
            echo $exception->getMessage() . PHP_EOL;
        }
    }

    private function markRelatedEntitiesAsDeleted(array $assets, string $entityClassName): void
    {
        if (!$assets) {
            return;
        }

        $externalIdsToRemove = array_column($assets, AbstractAssetMetadata::FIELD_OID);
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
            echo "Number of assets marked as deleted: $deletedEntitiesCount" . PHP_EOL;
            $this->entityManager->flush();
        }
    }
}
