<?php

namespace App\VersionOne\AssetMetadata\PrimaryWorkitem;

use App\VersionOne\AssetMetadata\AttributeInterface;

class TeamAttribute implements AttributeInterface
{
    public static function getName(): string
    {
        return 'Team';
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
        return 'Team';
    }
}
