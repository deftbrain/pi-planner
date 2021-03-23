<?php

namespace App\Integration\Jira;

use App\Entity\AbstractEntity;
use App\Entity\Workitem;
use App\Integration\AssetExporterInterface;
use App\Integration\Jira\Serializer\ObjectNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class AssetExporter implements AssetExporterInterface
{
    private ApiClient $apiClient;
    private SerializerInterface $serializer;

    public function __construct(ApiClient $apiClient, SerializerInterface $serializer)
    {
        $this->apiClient = $apiClient;
        $this->serializer = $serializer;
    }

    public function exportAsset(AbstractEntity $entity): void
    {
        if (!$entity instanceof Workitem) {
            return;
        }

        $values = $this->serializer->normalize(
            $entity,
            ObjectNormalizer::FORMAT,
            [ObjectNormalizer::GROUPS => ['writable'], ObjectNormalizer::PARENT_OBJECT_CLASS => get_class($entity)]
        );

        if ($entity->getExternalId()) {
            $this->apiClient->updateIssue($entity->getExternalId(), $values);
        } else {
            $externalId = $this->apiClient->createIssue($values);
            $entity->setExternalId($externalId);
        }
    }
}
