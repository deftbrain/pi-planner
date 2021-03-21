<?php

namespace App\Integration\VersionOne\AssetMetadata\Scope;

use App\Integration\VersionOne\AssetMetadata\AttributeInterface;

class ScheduleAttribute implements AttributeInterface
{
    public static function getName(): string
    {
        return 'Schedule';
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
        return 'Schedule';
    }
}
