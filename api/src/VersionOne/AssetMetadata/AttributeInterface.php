<?php

namespace App\VersionOne\AssetMetadata;

interface AttributeInterface
{
    public function getName(): string;

    public function isMultiValue(): bool;

    public function isRelation(): bool;

    public function getRelatedAsset(): ?string;
}
