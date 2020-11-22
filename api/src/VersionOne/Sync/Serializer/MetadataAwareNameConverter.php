<?php

namespace App\VersionOne\Sync\Serializer;

use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactoryInterface;
use Symfony\Component\Serializer\NameConverter\AdvancedNameConverterInterface;

/**
 * Custom implementation of the MetadataAwareNameConverter.
 * It's not possible to use \Symfony\Component\Serializer\NameConverter\MetadataAwareNameConverter because
 * it caches results in static properties disregarding of the used format so when a PUT request comes to the app:
 *  1. An entity is normalized to the VersionOne format using MetadataAwareNameConverter and props mapping is cached
 *  2. An update request to the VersionOne API
 *  3. The updated entity is normalized to the app format incorrectly by MetadataAwareNameConverter because of caching
 */
class MetadataAwareNameConverter implements AdvancedNameConverterInterface
{
    private ClassMetadataFactoryInterface $classMetadataFactory;

    public function __construct(ClassMetadataFactoryInterface $metadataFactory)
    {
        $this->classMetadataFactory = $metadataFactory;
    }

    /**
     * @inheritDoc
     */
    public function normalize(
        string $propertyName,
        string $class = null,
        string $format = null,
        array $context = []
    ): string {
        if (!$this->classMetadataFactory->hasMetadataFor($class)) {
            return $propertyName;
        }

        $attributesMetadata = $this->classMetadataFactory->getMetadataFor($class)->getAttributesMetadata();
        foreach ($attributesMetadata as $attributeMetadata) {
            if ($attributeMetadata->getName() === $propertyName) {
                return $attributeMetadata->getSerializedName() ?? $propertyName;
            }
        }

        return $propertyName;
    }

    /**
     * @inheritDoc
     */
    public function denormalize(
        string $propertyName,
        string $class = null,
        string $format = null,
        array $context = []
    ): string {
        if (!$this->classMetadataFactory->hasMetadataFor($class)) {
            return $propertyName;
        }

        $attributesMetadata = $this->classMetadataFactory->getMetadataFor($class)->getAttributesMetadata();
        foreach ($attributesMetadata as $attributeMetadata) {
            if ($propertyName === $attributeMetadata->getSerializedName()) {
                return $attributeMetadata->getName();
            }
        }

        return $propertyName;
    }
}
