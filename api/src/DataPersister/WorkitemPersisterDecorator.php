<?php

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Entity\Workitem;
use App\Integration\AssetExporterInterface;

class WorkitemPersisterDecorator implements ContextAwareDataPersisterInterface
{
    private ContextAwareDataPersisterInterface $decorated;
    private AssetExporterInterface $assetExporter;

    public function __construct(ContextAwareDataPersisterInterface $decorated, AssetExporterInterface $assetExporter)
    {
        $this->decorated = $decorated;
        $this->assetExporter = $assetExporter;
    }

    public function supports($data, array $context = []): bool
    {
        return $this->decorated->supports($data, $context);
    }

    public function persist($data, array $context = [])
    {
        if (
            $data instanceof Workitem && (
                ($context['collection_operation_name'] ?? null) === 'post'
                || ($context['item_operation_name'] ?? null) === 'patch'
            )
        ) {
            $this->assetExporter->exportAsset($data);
        }

        return $this->decorated->persist($data, $context);
    }

    public function remove($data, array $context = [])
    {
        return $this->decorated->remove($data, $context);
    }
}
