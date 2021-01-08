<?php

namespace App\VersionOne\Sync\AssetImporter;

class ScopeAssetImporter extends AssetImporter
{
    protected function importAssets(array $filter): void
    {
        parent::importAssets(['AssetState' => 64]);
    }
}
