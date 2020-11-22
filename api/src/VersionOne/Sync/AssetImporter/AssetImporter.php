<?php

namespace App\VersionOne\Sync\AssetImporter;

use App\Entity\AbstractEntity;
use App\VersionOne\AssetMetadata\AbstractAssetMetadata;
use App\VersionOne\AssetMetadata\AssetMetadataInterface;
use App\VersionOne\AssetMetadata\BaseAsset\IsDeletedAttribute;
use App\VersionOne\BulkApiClient;
use App\VersionOne\Sync\Serializer\Normalizer;
use Doctrine\ORM\EntityManagerInterface;
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

    public function __construct(
        BulkApiClient $apiClient,
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer,
        ClassMetadataFactoryInterface $classMetadataFactory
    ) {
        $this->apiClient = $apiClient;
        $this->entityManager = $entityManager;
        $this->serializer = $serializer;
        $this->classMetadataFactory = $classMetadataFactory;
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
        $entityClassName = $this->getEntityClass($assetType);
        [$relevantAssets, $deletedAssets] = $this->apiClient->find($relevantAssetsQuery, $deletedAssetsQuery);
        foreach ($relevantAssets as $asset) {
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
            } catch (\DomainException $exception) {
                echo $exception->getMessage() . PHP_EOL;
            }
        }

        $externalIdsToRemove = array_column($deletedAssets, AbstractAssetMetadata::FIELD_OID);
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

    private function getEntityClass(string $assetType): string
    {
        return $this->classMetadataFactory
            ->getMetadataFor(AbstractEntity::class)
            ->getClassDiscriminatorMapping()
            ->getClassForType($assetType);
    }
}
