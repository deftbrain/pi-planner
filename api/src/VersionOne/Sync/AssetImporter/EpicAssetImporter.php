<?php

namespace App\VersionOne\Sync\AssetImporter;

use App\Entity\EpicStatus;
use App\Entity\ProgramIncrement;
use App\VersionOne\AssetMetadata\Epic\ScopeAttribute;
use App\VersionOne\AssetMetadata\Epic\StatusAttribute;

class EpicAssetImporter extends AssetImporter
{
    public function import(): void
    {
        /** @var ProgramIncrement[] $programIncrements */
        $programIncrements = $this->entityManager->getRepository(ProgramIncrement::class)->findAll();
        foreach ($programIncrements as $programIncrement) {
            $epicStatuses = array_map(
                fn(EpicStatus $es) => $es->getExternalId(),
                $programIncrement->getEpicStatuses()->toArray()
            );
            $filter = [
                ScopeAttribute::getName() => $programIncrement->getProject()->getExternalId(),
                StatusAttribute::getName() => $epicStatuses,
            ];
            $this->importAssets($filter);
        }
    }
}
