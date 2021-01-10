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
}
