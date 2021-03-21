<?php

namespace App\Integration\VersionOne\AssetMetadata\Timebox;

use App\Integration\VersionOne\AssetMetadata\AttributeInterface;

class EndDateAttribute implements AttributeInterface
{
    public static function getName(): string
    {
        return 'EndDate';
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
