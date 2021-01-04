<?php

namespace App\VersionOne\Sync\AssetImporter;

class ScopeAssetImporter extends AssetImporter
{
    public function import(): void
    {
        $this->importAssets(['AssetState' => 64]);
    }
}
