<?php

namespace App\VersionOne\AssetMetadata;

interface AssetMetadataInterface
{
    public function getType(): string;

    /**
     * @return AttributeInterface[]
     */
    public function getAttributes(): array;
}
