<?php

namespace App\VersionOne\AssetMetadata;

class Workitem extends Asset
{
    public const ATTRIBUTE_SCOPE = 'Scope';
    public const ATTRIBUTE_SUPER = 'Super';
    public const ATTRIBUTE_TEAM = 'Team';
    public const ATTRIBUTE_PARENT = 'Parent';
    public const ATTRIBUTE_TIMEBOX = 'Timebox';

    public static function getType(): string
    {
        return 'PrimaryWorkitem';
    }

    public static function getAttributesToSelect(): array
    {
        return array_merge(
            parent::getAttributesToSelect(),
            [
                BacklogGroup::class => self::ATTRIBUTE_PARENT,
                Epic::class => self::ATTRIBUTE_SUPER,
                Project::class => self::ATTRIBUTE_SCOPE,
                Sprint::class => self::ATTRIBUTE_TIMEBOX,
                Team::class => self::ATTRIBUTE_TEAM,
            ]
        );
    }

    public static function getAssetToEntityPropertyMap(): array
    {
        return array_merge(
            parent::getAssetToEntityPropertyMap(),
            [
                self::ATTRIBUTE_PARENT => 'backlogGroup',
                self::ATTRIBUTE_SUPER => 'epic',
                self::ATTRIBUTE_SCOPE => 'project',
                self::ATTRIBUTE_TIMEBOX => 'sprint',
                self::ATTRIBUTE_TEAM => 'team',
            ]
        );
    }
}
