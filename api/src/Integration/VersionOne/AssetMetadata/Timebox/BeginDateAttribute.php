<?php

namespace App\Integration\VersionOne\AssetMetadata\Timebox;

use App\Integration\VersionOne\AssetMetadata\AttributeInterface;

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
