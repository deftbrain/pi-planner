<?php

namespace App\VersionOne\Message;

class ImportAssetsMessage
{
    private string $assetType;
    private bool $isForceUpdateRequired;

    public function __construct(string $assetType, bool $isForceUpdateRequired)
    {
        $this->assetType = $assetType;
        $this->isForceUpdateRequired = $isForceUpdateRequired;
    }

    public function getAssetType(): string
    {
        return $this->assetType;
    }

    public function isForceUpdateRequired(): bool
    {
        return $this->isForceUpdateRequired;
    }
}
