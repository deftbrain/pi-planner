<?php

namespace App\Integration\VersionOne\AssetMetadata\PrimaryWorkitem;

use App\Integration\VersionOne\AssetMetadata\AttributeInterface;

class DependenciesAttribute implements AttributeInterface
{
    public static function getName(): string
    {
        return 'Dependencies';
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
        return 'PrimaryWorkitem';
    }
}
