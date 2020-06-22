<?php

namespace App\Entity\Listener;

use App\Entity\AbstractEntity;
use App\VersionOne\AssetDetailUrlProvider;
use App\VersionOne\Sync\AssetExporter;

class AbstractEntityListener
{
    /**
     * @var AssetDetailUrlProvider
     */
    private $urlProvider;

    /**
     * @var AssetExporter
     */
    private $assetExporter;

    public function __construct(AssetDetailUrlProvider $urlProvider, AssetExporter $assetExporter)
    {
        $this->urlProvider = $urlProvider;
        $this->assetExporter = $assetExporter;
    }

    public function postLoad(AbstractEntity $entity): void
    {
        $entity->setExternalUrl(
            $this->urlProvider->getUrl($entity->getExternalId())
        );
    }

    public function preUpdate(AbstractEntity $entity): void
    {
        if (PHP_SAPI !== 'cli') {
            // Prevent exporting to V1 updates given from V1 itself during importing (can be run via cli only)
            $this->assetExporter->exportAsset($entity);
        }
    }
}
