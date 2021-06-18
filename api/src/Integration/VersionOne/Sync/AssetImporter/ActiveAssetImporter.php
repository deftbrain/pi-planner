<?php

namespace App\Integration\VersionOne\Sync\AssetImporter;

class ActiveAssetImporter extends AssetImporter
{
    protected function importAssets(array $filter): void
    {
        parent::importAssets(array_merge(['AssetState' => 64], $filter));
    }
}
