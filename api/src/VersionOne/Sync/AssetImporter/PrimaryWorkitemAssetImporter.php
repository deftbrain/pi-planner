<?php

namespace App\VersionOne\Sync\AssetImporter;

use App\Entity\Epic;
use App\VersionOne\AssetMetadata\PrimaryWorkitem\SuperAttribute;

class PrimaryWorkitemAssetImporter extends AssetImporter
{
    public function import(): void
    {
        $epics = $this->entityManager->getRepository(Epic::class)->findAll();
        foreach ($epics as $epic) {
            $this->importAssets([SuperAttribute::getName() => $epic->getExternalId()]);
        }
    }
}
