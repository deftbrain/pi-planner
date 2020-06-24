<?php

namespace App\VersionOne\AssetMetadata;

abstract class Asset
{
    /** @var string This attribute is automatically added to every asset returned by VersionOne API */
    public const ATTRIBUTE_ID = '_oid';
    public const ATTRIBUTE_NAME = 'Name';
    public const ATTRIBUTE_CHANGE_DATE = 'ChangeDateUTC';
    public const ATTRIBUTE_IS_DELETED = 'IsDeleted';

    abstract public static function getType(): string;

    public static function getAttributesToSelect(): array
    {
        return [
            self::ATTRIBUTE_NAME,
            self::ATTRIBUTE_CHANGE_DATE,
            self::ATTRIBUTE_IS_DELETED,
        ];
    }

    public static function getAssetToEntityPropertyMap(): array
    {
        return [
            self::ATTRIBUTE_ID => 'externalId',
            self::ATTRIBUTE_NAME => 'name',
            self::ATTRIBUTE_CHANGE_DATE => 'changedAt',
            self::ATTRIBUTE_IS_DELETED => 'isDeleted',
        ];
    }
}
