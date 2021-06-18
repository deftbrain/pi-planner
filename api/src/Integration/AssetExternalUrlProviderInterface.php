<?php

namespace App\Integration;

interface AssetExternalUrlProviderInterface
{
    public function getUrl(string $externalId): string;
}
