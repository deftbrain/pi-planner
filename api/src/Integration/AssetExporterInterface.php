<?php

namespace App\Integration;

use App\Entity\AbstractEntity;

interface AssetExporterInterface
{
    public function exportAsset(AbstractEntity $entity): void;
}
