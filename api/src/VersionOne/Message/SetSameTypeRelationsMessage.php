<?php

namespace App\VersionOne\Message;

class SetSameTypeRelationsMessage
{
    private array $asset;
    private string $entityClassName;

    public function __construct(array $asset, string $entityClassName)
    {
        $this->asset = $asset;
        $this->entityClassName = $entityClassName;
    }

    public function getAsset(): array
    {
        return $this->asset;
    }

    public function getEntityClassName(): string
    {
        return $this->entityClassName;
    }
}
