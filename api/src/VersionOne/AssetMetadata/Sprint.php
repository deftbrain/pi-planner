<?php

namespace App\VersionOne\AssetMetadata;

class Sprint extends Asset
{
    public const ATTRIBUTE_BEGIN_DATE = 'BeginDate';
    public const ATTRIBUTE_END_DATE = 'EndDate';
    public const ATTRIBUTE_SCHEDULE = 'Schedule';

    public static function getType(): string
    {
        return 'Timebox';
    }

    public static function getAttributesToSelect(): array
    {
        return array_merge(
            parent::getAttributesToSelect(),
            [
                self::ATTRIBUTE_BEGIN_DATE,
                self::ATTRIBUTE_END_DATE,
                SprintSchedule::class => self::ATTRIBUTE_SCHEDULE,
            ]
        );
    }

    public static function getAssetToEntityPropertyMap(): array
    {
        return array_merge(
            parent::getAssetToEntityPropertyMap(),
            [
                self::ATTRIBUTE_BEGIN_DATE => 'startDate',
                self::ATTRIBUTE_END_DATE => 'endDate',
                self::ATTRIBUTE_SCHEDULE => 'schedule',
            ]
        );
    }
}
