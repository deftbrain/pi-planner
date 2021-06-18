<?php

namespace App\Integration\VersionOne\Sync;

use App\Entity;
use App\Integration\VersionOne\AssetMetadata;

abstract class AssetToEntityMap
{
    public const MAP = [
        AssetMetadata\BacklogGroup::class => Entity\BacklogGroup::class,
        AssetMetadata\Epic::class => Entity\Epic::class,
        AssetMetadata\EpicStatus::class => Entity\EpicStatus::class,
        AssetMetadata\Project::class => Entity\Project::class,
        AssetMetadata\Sprint::class => Entity\Sprint::class,
        AssetMetadata\SprintSchedule::class => Entity\SprintSchedule::class,
        AssetMetadata\Team::class => Entity\Team::class,
        AssetMetadata\Workitem::class => Entity\Workitem::class,
    ];
}
