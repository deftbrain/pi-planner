<?php

namespace App\VersionOne\Sync\Serializer;

use App\VersionOne\AssetMetadata\Asset;
use App\VersionOne\Sync\AssetToEntityMap;
use Symfony\Component\Serializer\NameConverter\AdvancedNameConverterInterface;

class NameConverter implements AdvancedNameConverterInterface
{
    /**
     * @inheritDoc
     */
    public function normalize($propertyName, string $class = null, string $format = null, array $context = [])
    {
        throw new \LogicException('Normalization is not supported');
    }

    /**
     * @inheritDoc
     */
    public function denormalize($propertyName, string $class = null, string $format = null, array $context = [])
    {
        /** @var Asset $assetMetadataClassName */
        $assetMetadataClassName = array_search($class, AssetToEntityMap::MAP);
        return $assetMetadataClassName::getAssetToEntityPropertyMap()[$propertyName] ?? $propertyName;
    }
}
