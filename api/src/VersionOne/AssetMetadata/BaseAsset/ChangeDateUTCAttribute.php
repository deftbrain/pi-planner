<?php

namespace App\VersionOne\AssetMetadata\BaseAsset;

use App\VersionOne\AssetMetadata\AttributeInterface;

class ChangeDateUTCAttribute implements AttributeInterface
{
    public static function getName(): string
    {
        return 'ChangeDateUTC';
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
