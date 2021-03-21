<?php

namespace App\Integration\VersionOne\AssetMetadata\Theme;

use App\Integration\VersionOne\AssetMetadata\AttributeInterface;

class ScopeChildrenMeAndDownAttribute implements AttributeInterface
{
    public static function getName(): string
    {
        return 'Scope.ChildrenMeAndDown';
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
