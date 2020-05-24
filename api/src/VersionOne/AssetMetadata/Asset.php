<?php

namespace App\VersionOne\AssetMetadata;

abstract class Asset
{
    /** @var string This attribute is automatically added to every asset returned by VersionOne API */
    public const ATTRIBUTE_ID = '_oid';
    public const ATTRIBUTE_NAME = 'Name';
    public const ATTRIBUTE_STATE = 'AssetState';
    public const ATTRIBUTE_STATE_ACTIVE = 'Active';
    public const ATTRIBUTE_STATE_FUTURE = 'Future';

    abstract public static function getType(): string;

    public static function getAttributesToSelect(): array
    {
        return [self::ATTRIBUTE_NAME];
    }

    public static function getAssetToEntityPropertyMap(): array
    {
        return [
            self::ATTRIBUTE_ID => 'externalId',
            self::ATTRIBUTE_NAME => 'name',
        ];
    }
}
