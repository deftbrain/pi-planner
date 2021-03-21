<?php

namespace App\Integration\VersionOne\AssetMetadata\Epic;

use App\Integration\VersionOne\AssetMetadata\AttributeInterface;

class WsjfAttribute implements AttributeInterface
{
    public static function getName(): string
    {
        return 'Wsjf';
    }

    public function isMultiValue(): bool
    {
        return false;
    }

    public function isRelation(): bool
    {
        return false;
    }

    public function getRelatedAsset(): ?string
    {
        return null;
    }
}
