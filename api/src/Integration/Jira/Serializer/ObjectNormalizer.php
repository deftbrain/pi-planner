<?php

namespace App\Integration\Jira\Serializer;

use App\Entity\AbstractEntity;
use App\Entity\BacklogGroup;
use App\Entity\Epic;
use App\Entity\Sprint;
use App\Integration\Serializer\ObjectNormalizer as BaseObjectNormalizer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\PropertyInfo\PropertyTypeExtractorInterface;
use Symfony\Component\Serializer\Mapping\ClassDiscriminatorResolverInterface;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactoryInterface;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ObjectNormalizer extends BaseObjectNormalizer
{
    public const FORMAT = 'jira';
    private const FIELD_ID = 'id';
    private const FIELD_KEY = 'key';
    private const FIELD_UPDATED = 'updated';

    private ValidatorInterface $validator;
    private EntityManagerInterface $entityManager;
    private array $classDiscriminatorMapping;

    /** @var callable */
    private $objectClassResolver = '\Doctrine\Common\Util\ClassUtils::getClass';

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
        return $data instanceof AbstractEntity && self::FORMAT === $format;
    }

    /**
     * @inheritDoc
     */
    public function supportsDenormalization($data, string $type, string $format = null, array $context = []): bool
    {
        return ($type === AbstractEntity::class || in_array($type, $this->classDiscriminatorMapping, true))
            && self::FORMAT === $format;
    }

    /**
     * @inheritDoc
     */
    public function denormalize($data, string $type, string $format = null, array $context = [])
    {
        if (!$data) {
            return null;
        }

        $entityExternalId = (is_string($data) || is_numeric($data))
            ? $data
            : ($data[self::FIELD_KEY] ?? $data[self::FIELD_ID]);
        $existingEntity = $this->findEntity($type, $entityExternalId);
        $isParentObject = isset($context[self::PARENT_OBJECT_CLASS]) && $context[self::PARENT_OBJECT_CLASS] === $type;
        if ($isParentObject) {
            if (isset($data['fields'])) {
                $data += $data['fields'];
                unset($data['fields']);
            }
            if (isset($data['renderedFields']['description'])) {
                // Replace description in the Atlassian Document Format with the HTML representation
                $data['description'] = $data['renderedFields']['description'];
                unset($data['renderedFields']);
            }
            if (!$existingEntity) {
                $context[self::GROUPS][] = 'readable_on_create';
            }
        } else {
            if ($existingEntity) {
                return $existingEntity;
            }

            // TODO: Store information about related objects that are out-of-scope to display a warning in a UI
            return null;
        }

        if ($existingEntity) {
            if (isset($data[self::FIELD_UPDATED]) && empty($context[self::FORCE_UPDATE])) {
                $changedAt = new \DateTimeImmutable($data[self::FIELD_UPDATED]);
                if ($changedAt === $existingEntity->getChangedAt()) {
                    return $existingEntity;
                }
            }

            $context[self::OBJECT_TO_POPULATE] = $existingEntity;
        }

        $entity = parent::denormalize($data, $type, $format, $context);
        $errors = $this->validator->validate($entity);
        if ($errors->count()) {
            throw new \DomainException(sprintf("Unable to import the asset: %s.\n%s", json_encode($data), $errors));
        }

        return $entity;
    }

    private function findEntity(string $entityClassName, string $externalId): ?AbstractEntity
    {
        return $this->entityManager->getRepository($entityClassName)->findOneBy(['externalId' => $externalId]);
    }

    /**
     * @param AbstractEntity $object
     */
    public function normalize($object, string $format = null, array $context = [])
    {
        $isTopLevelSerializedObject = ($this->objectClassResolver)($object) === $context[self::PARENT_OBJECT_CLASS];
        if (!$isTopLevelSerializedObject) {
            if ($object instanceof Sprint) {
                return (int) $object->getExternalId();
            }

            if ($object instanceof Epic) {
                return $object->getExternalId();
            }
        }

        $data = parent::normalize($object, $format, $context);
        unset($data['type']);
        if (!$isTopLevelSerializedObject && ($object instanceof BacklogGroup)) {
            return [$data];
        }

        if ($isTopLevelSerializedObject && isset($data['issuetype'])) {
            $data['issuetype'] = ['name' => $data['issuetype']];
        }

        return $data;
    }
}
