<?php

namespace App\Integration\VersionOne\AssetMetadata;

interface AttributeInterface
{
    public static function getName(): string;

    public function isMultiValue(): bool;

    public function isRelation(): bool;

    public function getRelatedAsset(): ?string;
}
