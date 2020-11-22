<?php

namespace App\VersionOne\AssetMetadata\Timebox;

use App\VersionOne\AssetMetadata\AttributeInterface;

class BeginDateAttribute implements AttributeInterface
{
    public static function getName(): string
    {
        return 'BeginDate';
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
