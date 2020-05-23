<?php

namespace App\VersionOne\AssetMetadata;

class SprintSchedule extends Asset
{
    public static function getType(): string
    {
        return 'Schedule';
    }
}
