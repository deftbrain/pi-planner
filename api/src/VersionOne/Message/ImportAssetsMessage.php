<?php

namespace App\VersionOne\Message;

class ImportAssetsMessage
{
    private ?string $assetType;

    public function __construct(string $assetType = null)
    {
        $this->assetType = $assetType;
    }

    public function getAssetType(): ?string
    {
        return $this->assetType;
    }
}
