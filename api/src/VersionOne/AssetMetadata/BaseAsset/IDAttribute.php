<?php

namespace App\VersionOne\AssetMetadata\BaseAsset;

use App\VersionOne\AssetMetadata\AttributeInterface;

class IDAttribute implements AttributeInterface
{
    public static function getName(): string
    {
        return 'ID';
    }

    public function isMultiValue(): bool
    {
        return false;
    }

    public function isRelation(): bool
    {
        return true;
    }

    public function getRelatedAsset(): ?string
    {
        return 'BaseAsset';
    }
}
