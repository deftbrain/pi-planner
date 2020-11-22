<?php

namespace App\VersionOne\Sync;

use App\Entity\AbstractEntity;
use App\VersionOne\BulkApiClient;
use App\VersionOne\Sync\Serializer\MaxDepthHandler;
use App\VersionOne\Sync\Serializer\Normalizer;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class AssetExporter
{
    /**
     * @var BulkApiClient
     */
    private $apiClient;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    public function __construct(
        BulkApiClient $apiClient,
        SerializerInterface $serializer
    ) {
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
                    Normalizer::GROUPS => ['writable'],
                    Normalizer::MAX_DEPTH_HANDLER => new MaxDepthHandler,
                    Normalizer::ENABLE_MAX_DEPTH => true,
                    Normalizer::PARENT_OBJECT_CLASS => get_class($entity),
                ]
            );
            $this->apiClient->updateAsset($entity->getExternalId(), $values);
        }
    }
}
