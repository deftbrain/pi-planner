<?php

namespace App\Integration\VersionOne\Listener;

use App\Integration\VersionOne\Message\ImportAllTypesAssetsMessage;
use Symfony\Component\Messenger\MessageBusInterface;

class ProjectSettingsEntityListener
{
    private MessageBusInterface $bus;

    public function __construct(MessageBusInterface $bus)
    {
        $this->bus = $bus;
    }

    public function importEntities(): void
    {
        $this->bus->dispatch(new ImportAllTypesAssetsMessage);
    }
}
