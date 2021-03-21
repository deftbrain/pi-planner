<?php

namespace App\Integration\VersionOne\Sync\AssetImporter;

use App\Entity\Epic;
use App\Entity\ProjectSettings;
use App\Integration\VersionOne\AssetMetadata\PrimaryWorkitem\SuperAttribute;

class PrimaryWorkitemAssetImporter extends AssetImporter
{
    protected function importAssets(array $filter): void
    {
        /** @var ProjectSettings[] $projectsSettings */
        $projectsSettings = $this->entityManager->getRepository(ProjectSettings::class)->findAll();
        foreach ($projectsSettings as $projectSettings) {
            $epicExternalIds = array_map(
                fn(Epic $epic) => $epic->getExternalId(),
                $projectSettings->getEpics()->toArray()
            );
            if ($epicExternalIds) {
                parent::importAssets([SuperAttribute::getName() => $epicExternalIds]);
            }
        }
    }
}
