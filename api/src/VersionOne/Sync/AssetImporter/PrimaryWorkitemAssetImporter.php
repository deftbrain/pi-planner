<?php

namespace App\VersionOne\Sync\AssetImporter;

use App\Entity\Epic;
use App\Entity\ProjectSettings;
use App\VersionOne\AssetMetadata\PrimaryWorkitem\SuperAttribute;

class PrimaryWorkitemAssetImporter extends AssetImporter
{
    public function import(): void
    {
        /** @var ProjectSettings[] $projectsSettings */
        $projectsSettings = $this->entityManager->getRepository(ProjectSettings::class)->findAll();
        foreach ($projectsSettings as $projectSettings) {
            $epicExternalIds = array_map(
                fn(Epic $epic) => $epic->getExternalId(),
                $projectSettings->getEpics()->toArray()
            );
            if ($epicExternalIds) {
                $this->importAssets([SuperAttribute::getName() => $epicExternalIds]);
            }
        }
    }
}
