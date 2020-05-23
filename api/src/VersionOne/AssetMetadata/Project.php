<?php

namespace App\VersionOne\AssetMetadata;

class Project extends Asset
{
    public const ATTRIBUTE_SCHEDULE = 'Schedule';

    public static function getType(): string
    {
        return 'Scope';
    }

    public static function getAttributesToSelect(): array
    {
        return array_merge(
            parent::getAttributesToSelect(),
            [
                SprintSchedule::class => self::ATTRIBUTE_SCHEDULE,
            ]
        );
    }

    public static function getAssetToEntityPropertyMap(): array
    {
        return array_merge(
            parent::getAssetToEntityPropertyMap(),
            [
                self::ATTRIBUTE_SCHEDULE => 'sprintSchedule',
            ]
        );
    }
}
