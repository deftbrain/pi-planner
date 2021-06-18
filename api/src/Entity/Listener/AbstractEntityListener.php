<?php

namespace App\Entity\Listener;

use App\Entity\AbstractEntity;
use App\Integration\AssetExternalUrlProviderInterface;

class AbstractEntityListener
{
    private AssetExternalUrlProviderInterface $urlProvider;

    public function __construct(AssetExternalUrlProviderInterface $urlProvider)
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
