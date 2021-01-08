<?php

namespace App\VersionOne\Sync\AssetImporter;

use App\Entity\Project;
use App\VersionOne\AssetMetadata\Epic\ScopeAttribute;

class EpicAssetImporter extends AssetImporter
{
    protected function importAssets(array $filter): void
    {
        /** @var Project[] $projects */
        $projects = $this->entityManager->getRepository(Project::class)->findAll();
        foreach ($projects as $project) {
            $filter = ['AssetState' => 64, ScopeAttribute::getName() => $project->getExternalId()];
            parent::importAssets($filter);
        }
    }
}
