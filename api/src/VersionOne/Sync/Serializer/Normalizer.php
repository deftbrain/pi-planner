<?php

namespace App\VersionOne\Sync\Serializer;

use App\Entity\AbstractEntity;
use App\VersionOne\AssetMetadata\Asset;
use App\VersionOne\Sync\AssetToEntityMap;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\PropertyInfo\PropertyTypeExtractorInterface;
use Symfony\Component\Serializer\Mapping\ClassDiscriminatorResolverInterface;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactoryInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class Normalizer extends ObjectNormalizer
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    public function __construct(
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator,
        ClassMetadataFactoryInterface $classMetadataFactory = null,
        NameConverter $nameConverter = null,
        PropertyAccessorInterface $propertyAccessor = null,
        PropertyTypeExtractorInterface $propertyTypeExtractor = null,
        ClassDiscriminatorResolverInterface $classDiscriminatorResolver = null,
        callable $objectClassResolver = null,
        array $defaultContext = []
    ) {
        parent::__construct(
            $classMetadataFactory,
            $nameConverter,
            $propertyAccessor,
            $propertyTypeExtractor,
            $classDiscriminatorResolver,
            $objectClassResolver,
            $defaultContext
        );

        $this->entityManager = $entityManager;
        $this->validator = $validator;
    }

    /**
     * @inheritDoc
     */
    public function supportsNormalization($data, string $format = null): bool
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function supportsDenormalization($data, string $type, string $format = null, array $context = []): bool
    {
        return in_array($type, AssetToEntityMap::MAP, true);
    }

    /**
     * @inheritDoc
     */
    public function denormalize($data, string $type, string $format = null, array $context = [])
    {
        $existingEntity = $this->findEntity($type, $data[Asset::ATTRIBUTE_ID]);
        if ($existingEntity) {
            return $existingEntity;
        }

        $data = array_map([$this, 'denormalizeAttributeValue'], $data);
        $entity = parent::denormalize($data, $type, $format, $context);
        $errors = $this->validator->validate($entity);
        if ($errors->count()) {
            throw new \DomainException($errors . PHP_EOL . 'Related asset: ' . json_encode($data));
        }

        return $entity;
    }

    private function denormalizeAttributeValue($value)
    {
        if (isset($value[Asset::ATTRIBUTE_ID]) && 'NULL' === $value[Asset::ATTRIBUTE_ID]) {
            return null;
        }

        return $value;
    }

    private function findEntity(string $entityClassName, string $versionOneId): ?AbstractEntity
    {
        return $this->entityManager->getRepository($entityClassName)->findOneBy(['externalId' => $versionOneId]);
    }
}
