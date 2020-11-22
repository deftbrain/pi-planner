<?php

namespace App\VersionOne\AssetMetadata;

abstract class AbstractAssetMetadata implements AssetMetadataInterface
{
    /** @var string This attribute is automatically added to every asset returned by VersionOne API */
    public const FIELD_OID = '_oid';

    /**
     * @var AttributeInterface[]
     */
    protected array $attributes = [];

    /**
     * @inheritDoc
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }
}
