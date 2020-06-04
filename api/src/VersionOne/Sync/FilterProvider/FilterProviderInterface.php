<?php

namespace App\VersionOne\Sync\FilterProvider;

interface FilterProviderInterface
{
    public function getFilter(): array;
}
