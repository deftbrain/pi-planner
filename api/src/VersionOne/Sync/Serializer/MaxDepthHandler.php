<?php

namespace App\VersionOne\Sync\Serializer;

use App\Entity\AbstractEntity;

class MaxDepthHandler
{
    public function __invoke(AbstractEntity $innerObject): string
    {
        return $innerObject->getExternalId();
    }
}
