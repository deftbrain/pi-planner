<?php

namespace App\VersionOne\Sync\AssetImporter;

use App\Entity\Project;
use App\VersionOne\AssetMetadata\Epic\ScopeAttribute;

class EpicAssetImporter extends AssetImporter
{
    public function import(): void
    {
        /** @var Project[] $projects */
        $projects = $this->entityManager->getRepository(Project::class)->findAll();
        foreach ($projects as $project) {
            $filter = ['AssetState' => 64, ScopeAttribute::getName() => $project->getExternalId()];
            $this->importAssets($filter);
        }
    }
}
