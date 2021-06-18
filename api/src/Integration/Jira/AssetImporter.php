<?php

namespace App\Integration\Jira;

use App\Entity\AbstractEntity;
use App\Integration\Jira\Serializer\ObjectNormalizer;
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

    public function persistAssets(array $assets, string $type, bool $isForceUpdateEnabled = false): void
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
                        ObjectNormalizer::FORCE_UPDATE => $isForceUpdateEnabled,
                    ]
                );
                if ($entity) {
                    $this->entityManager->persist($entity);
                }
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
