<?php

namespace App\Integration\VersionOne\AssetMetadata\BaseAsset;

use App\Integration\VersionOne\AssetMetadata\AttributeInterface;

class NameAttribute implements AttributeInterface
{
    public static function getName(): string
    {
        return 'Name';
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
