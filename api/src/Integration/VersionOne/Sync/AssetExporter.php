<?php

namespace App\Integration\VersionOne\Sync;

use App\Entity\AbstractEntity;
use App\Entity\Workitem;
use App\Integration\AssetExporterInterface;
use App\Integration\VersionOne\BulkApiClient;
use App\Integration\VersionOne\Sync\Serializer\Normalizer;
use Symfony\Component\Serializer\SerializerInterface;

class AssetExporter implements AssetExporterInterface
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
        $values = $this->serializer->normalize(
            $entity,
            Normalizer::FORMAT_V1_JSON,
            [
                Normalizer::GROUPS => ['writable'],
                Normalizer::PARENT_OBJECT_CLASS => get_class($entity),
            ]
        );

        if ($entity instanceof Workitem) {
            // TODO: Move to appropriate place
            $values['OriginalEstimate'] = $entity->getEstimateFrontend() + $entity->getEstimateBackend();
        }

        if ($entity->getExternalId()) {
            $this->apiClient->updateAsset($entity->getExternalId(), $values);
        } else {
            if (!$entity instanceof Workitem) {
                throw new \LogicException('Only Workitem entities can be exported to VersionOne');
            }

            $externalId = $this->apiClient->createAsset('Story', $values);
            $entity->setExternalId($externalId);
        }
    }
}
