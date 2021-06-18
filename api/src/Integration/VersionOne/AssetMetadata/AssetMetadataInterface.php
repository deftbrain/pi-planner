<?php

namespace App\Integration\VersionOne\AssetMetadata;

interface AssetMetadataInterface
{
    public function getType(): string;

    /**
     * @return AttributeInterface[]
     */
    public function getAttributes(): array;
}
