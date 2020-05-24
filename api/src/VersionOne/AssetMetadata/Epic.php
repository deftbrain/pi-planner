<?php

namespace App\VersionOne\AssetMetadata;

class Epic extends Asset
{
    public const ATTRIBUTE_SCOPE = 'Scope';
    public const ATTRIBUTE_STATUS = 'Status';
    public const ATTRIBUTE_WSJF = 'Wsjf';

    public static function getType(): string
    {
        return 'Epic';
    }

    public static function getAttributesToSelect(): array
    {
        return array_merge(
            parent::getAttributesToSelect(),
            [
                Project::class => self::ATTRIBUTE_SCOPE,
                EpicStatus::class => self::ATTRIBUTE_STATUS,
                self::ATTRIBUTE_WSJF,
            ]
        );
    }

    public static function getAssetToEntityPropertyMap(): array
    {
        return array_merge(
            parent::getAssetToEntityPropertyMap(),
            [
                self::ATTRIBUTE_SCOPE => 'project',
                self::ATTRIBUTE_STATUS => 'status',
                self::ATTRIBUTE_WSJF => 'wsjf',
            ]
        );
    }
}
