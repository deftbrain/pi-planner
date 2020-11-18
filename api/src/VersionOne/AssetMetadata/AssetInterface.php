<?php

namespace App\VersionOne\AssetMetadata;

interface AssetInterface
{
    /**
     * @return AttributeInterface[]
     */
    public function getAttributes(): array;
}
