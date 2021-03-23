<?php

namespace App\Integration\Jira;

use App\Entity\AbstractEntity;
use App\Integration\Jira\Serializer\ObjectNormalizer;
use App\Integration\VersionOne\AssetMetadata\AbstractAssetMetadata;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactoryInterface;
use Symfony\Component\Serializer\SerializerInterface;

class AssetImporter
{
    private SerializerInterface $serializer;
    private EntityManagerInterface $entityManager;
    private ClassMetadataFactoryInterface $classMetadataFactory;

    public function __construct(
        SerializerInterface $serializer,
        EntityManagerInterface $entityManager,
        ClassMetadataFactoryInterface $classMetadataFactory
    ) {
        $this->serializer = $serializer;
        $this->entityManager = $entityManager;
        $this->classMetadataFactory = $classMetadataFactory;
    }

    private function updateOrInsertAssets(array $assets, string $entityClassName): void
    {
        $assetsWithSameTypeRelations = [];
        $assetsWithoutSameTypeRelations = [];
        $assetType = $this->assetMetadata->getType();
        $sameTypeRelationsAttributes = array_filter(
            $this->assetMetadata->getAttributes(),
            fn ($a) => $a->isRelation() && $a->getRelatedAsset() === $assetType
        );
        if (!$sameTypeRelationsAttributes) {
            $this->persistAssets($assets, $entityClassName);
            return;
        }

        $sameTypeRelationAttributeNames = array_map(fn ($a) => $a::getName(), $sameTypeRelationsAttributes);
        foreach ($assets as $asset) {
            $assetsWithSameTypeRelations[] = array_intersect_key(
                $asset,
                array_flip($sameTypeRelationAttributeNames + [AbstractAssetMetadata::FIELD_OID])
            );
            $assetsWithoutSameTypeRelations[] = array_diff_key($asset, array_flip($sameTypeRelationAttributeNames));
        }

        $this->persistAssets($assetsWithoutSameTypeRelations, $entityClassName);
        $this->persistAssets($assetsWithSameTypeRelations, $entityClassName);
    }

    public function persistAssets(array $assets, string $type): void
    {
        $entityClassName = $this->getEntityClass($type);
        foreach ($assets as $asset) {
            try {
                $entity = $this->serializer->denormalize(
                    $asset,
                    $entityClassName,
                    ObjectNormalizer::FORMAT,
                    [
                        ObjectNormalizer::GROUPS => ['readable'],
                        ObjectNormalizer::DISABLE_TYPE_ENFORCEMENT => true,
                        ObjectNormalizer::PARENT_OBJECT_CLASS => $entityClassName,
                    ]
                );
                $this->entityManager->persist($entity);
            } catch (\DomainException $exception) {
                echo $exception->getMessage() . PHP_EOL;
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
