<?php

namespace App\VersionOne\AssetMetadata\Theme;

use App\VersionOne\AssetMetadata\AttributeInterface;

class ScopeChildrenAndMeAttribute implements AttributeInterface
{
    public static function getName(): string
    {
        return 'Scope.ChildrenAndMe';
    }

    public function isMultiValue(): bool
    {
        return true;
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
