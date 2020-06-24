<?php

namespace App\VersionOne\Sync;

use App\Entity\AbstractEntity;
use App\VersionOne\ApiClient;
use App\VersionOne\AssetMetadata\Asset;
use App\VersionOne\Sync\Serializer\Normalizer;
use Symfony\Component\Serializer\SerializerInterface;

class AssetExporter
{
    /**
     * @var ApiClient
     */
    private $apiClient;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    public function __construct(ApiClient $apiClient, SerializerInterface $serializer)
    {
        $this->apiClient = $apiClient;
        $this->serializer = $serializer;
    }

    public function exportAsset(AbstractEntity $entity): void
    {
        if ($entity->getExternalId()) {
            $values = $this->serializer->normalize(
                $entity,
                Normalizer::FORMAT_V1_JSON,
                [
                    Normalizer::PARENT_CLASS => get_class($entity),
                    Normalizer::ATTRIBUTES => $this->getAttributesToExport($entity),
                ]
            );
            $this->apiClient->updateAsset($entity->getExternalId(), $values);
        }
    }

    private function getAttributesToExport(AbstractEntity $entity)
    {
        /** @var Asset $assetMetadataClassName */
        $assetMetadataClassName = array_search(get_class($entity), AssetToEntityMap::MAP);
        $map = $assetMetadataClassName::getAssetToEntityPropertyMap();
        unset($map[Asset::ATTRIBUTE_ID], $map[Asset::ATTRIBUTE_CHANGE_DATE], $map[Asset::ATTRIBUTE_IS_DELETED]);
        return array_values($map);
    }
}
