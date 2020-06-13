<?php

namespace App\Entity\Listener;

use App\Entity\AbstractEntity;
use App\VersionOne\AssetDetailUrlProvider;

class AbstractEntityListener
{
    /**
     * @var AssetDetailUrlProvider
     */
    private $urlProvider;

    public function __construct(AssetDetailUrlProvider $urlProvider)
    {
        $this->urlProvider = $urlProvider;
    }

    public function postLoad(AbstractEntity $entity): void
    {
        $entity->setExternalUrl(
            $this->urlProvider->getUrl($entity->getExternalId())
        );
    }
}
