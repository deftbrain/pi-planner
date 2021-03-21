<?php

namespace App\Integration\VersionOne\Sync\Serializer;

use App\Entity\AbstractEntity;
use App\Integration\Serializer\ObjectNormalizer;
use App\Integration\VersionOne\AssetMetadata\AbstractAssetMetadata;
use App\Integration\VersionOne\AssetMetadata\BaseAsset\ChangeDateUTCAttribute;
use App\Integration\VersionOne\AssetMetadata\BaseAsset\IDAttribute;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\PropertyInfo\PropertyTypeExtractorInterface;
use Symfony\Component\Serializer\Mapping\ClassDiscriminatorResolverInterface;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactoryInterface;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class Normalizer extends ObjectNormalizer
{
    public const FORMAT_V1_JSON = 'v1+json';

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @var callable
     */
    private $objectClassResolver = '\Doctrine\Common\Util\ClassUtils::getClass';

    private array $classDiscriminatorMapping;

    public function __construct(
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator,
        ClassMetadataFactoryInterface $classMetadataFactory,
        NameConverterInterface $nameConverter = null,
        PropertyAccessorInterface $propertyAccessor = null,
        PropertyTypeExtractorInterface $propertyTypeExtractor = null,
        ClassDiscriminatorResolverInterface $classDiscriminatorResolver = null
    ) {
        parent::__construct(
            $classMetadataFactory,
            $nameConverter,
            $propertyAccessor,
            $propertyTypeExtractor,
            $classDiscriminatorResolver,
            $this->objectClassResolver
        );

        $this->entityManager = $entityManager;
        $this->validator = $validator;
        $this->classDiscriminatorMapping = $classMetadataFactory
            ->getMetadataFor(AbstractEntity::class)
            ->getClassDiscriminatorMapping()
            ->getTypesMapping();
    }

    /**
     * @inheritDoc
     */
    public function supportsNormalization($data, string $format = null): bool
    {
        return $data instanceof AbstractEntity && self::FORMAT_V1_JSON === $format;
    }

    /**
     * @inheritDoc
     */
    public function supportsDenormalization($data, string $type, string $format = null, array $context = []): bool
    {
        return ($type === AbstractEntity::class || in_array($type, $this->classDiscriminatorMapping, true))
            && self::FORMAT_V1_JSON === $format;
    }

    /**
     * @inheritDoc
     */
    public function denormalize($data, string $type, string $format = null, array $context = [])
    {
        if (null === $data) {
            return null;
        }

        $isRelatedAsset = count($data) === 1 && isset($data[AbstractAssetMetadata::FIELD_OID]);
        $existingEntity = $this->findEntity($type, $data[AbstractAssetMetadata::FIELD_OID]);
        if ($isRelatedAsset) {
            if ($existingEntity) {
                return $existingEntity;
            }

            // TODO: Store information about related objects that are out-of-scope to display a warning in a UI
            return null;
        }

        if ($existingEntity) {
            if (isset($data[ChangeDateUTCAttribute::getName()]) && empty($context[self::FORCE_UPDATE])) {
                $changedAt = strtotime($data[ChangeDateUTCAttribute::getName()]);
                // Use timestamps for comparison because VersionOne API returns the
                // change date-time with microseconds but in a DB we store only seconds
                $wasEntityChanged = $changedAt !== $existingEntity->getChangedAt()->getTimestamp();
                if (!$wasEntityChanged) {
                    return $existingEntity;
                }
            }

            $context[self::OBJECT_TO_POPULATE] = $existingEntity;
        }

        array_walk($data, [$this, 'denormalizeAttributeValue']);
        $entity = parent::denormalize($data, $type, $format, $context);
        $errors = $this->validator->validate($entity);
        if ($errors->count()) {
            throw new \DomainException(sprintf("Unable to import the asset: %s.\n%s", json_encode($data), $errors));
        }

        return $entity;
    }

    private function denormalizeAttributeValue(&$value, string $attribute)
    {
        if (isset($value[AbstractAssetMetadata::FIELD_OID])) {
            if ($attribute === IDAttribute::getName()) {
                $value = $value[AbstractAssetMetadata::FIELD_OID];
            } elseif ($value[AbstractAssetMetadata::FIELD_OID] === 'NULL') {
                $value = null;
            }
        } elseif (in_array($value, ['True', 'False'], true)) {
            $value = 'True' === $value;
        }
    }

    private function findEntity(string $entityClassName, string $versionOneId): ?AbstractEntity
    {
        return $this->entityManager->getRepository($entityClassName)->findOneBy(['externalId' => $versionOneId]);
    }

    /**
     * @param AbstractEntity $object
     */
    public function normalize($object, string $format = null, array $context = [])
    {
        if (($this->objectClassResolver)($object) === $context[self::PARENT_OBJECT_CLASS]) {
            return parent::normalize($object, $format, $context);
        }

        return $object->getExternalId();
    }
}
