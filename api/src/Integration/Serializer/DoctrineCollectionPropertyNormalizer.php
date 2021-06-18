<?php

namespace App\Integration\Serializer;

use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactoryInterface;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;

class DoctrineCollectionPropertyNormalizer implements ContextAwareNormalizerInterface
{
    private ClassMetadataFactoryInterface $classMetadataFactory;

    public function __construct(ClassMetadataFactoryInterface $classMetadataFactory)
    {
        $this->classMetadataFactory = $classMetadataFactory;
    }

    public function supportsNormalization($data, string $format = null, array $context = [])
    {
        return ($data instanceof Collection)
            && isset($context[ObjectNormalizer::PARENT_OBJECT_CLASS])
            && $this->isParentObjectClassSupported($context[ObjectNormalizer::PARENT_OBJECT_CLASS]);
    }

    private function isParentObjectClassSupported(string $className): bool
    {
        return $this->classMetadataFactory->hasMetadataFor($className);
    }

    public function normalize($object, string $format = null, array $context = [])
    {
        $result = [];
        foreach ($object as $item) {
            $result[] = $item->getExternalId();
        }

        return $result;
    }
}
