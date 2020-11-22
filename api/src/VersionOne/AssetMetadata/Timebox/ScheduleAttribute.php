<?php

namespace App\VersionOne\AssetMetadata\Timebox;

use App\VersionOne\AssetMetadata\AttributeInterface;

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
