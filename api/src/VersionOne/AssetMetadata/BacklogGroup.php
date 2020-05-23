<?php

namespace App\VersionOne\AssetMetadata;

class BacklogGroup extends Asset
{
    public const ATTRIBUTE_SCOPE = 'Scope';

    public static function getType(): string
    {
        return 'Theme';
    }

    public static function getAttributesToSelect(): array
    {
        return array_merge(
            parent::getAttributesToSelect(),
            [
                Project::class => self::ATTRIBUTE_SCOPE,
            ]
        );
    }

    public static function getAssetToEntityPropertyMap(): array
    {
        return array_merge(
            parent::getAssetToEntityPropertyMap(),
            [
                self::ATTRIBUTE_SCOPE => 'project',
            ]
        );
    }


}
