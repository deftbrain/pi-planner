<?php

namespace App\VersionOne\AssetMetadata\PrimaryWorkitem;

use App\VersionOne\AssetMetadata\AttributeInterface;

class SuperAttribute implements AttributeInterface
{
    public static function getName(): string
    {
        return 'Super';
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
        return 'Epic';
    }
}
