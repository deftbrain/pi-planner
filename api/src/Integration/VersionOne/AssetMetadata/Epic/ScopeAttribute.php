<?php

namespace App\Integration\VersionOne\AssetMetadata\Epic;

use App\Integration\VersionOne\AssetMetadata\AttributeInterface;

class ScopeAttribute implements AttributeInterface
{
    public static function getName(): string
    {
        return 'Scope';
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
        return 'Scope';
    }
}
