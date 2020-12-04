<?php

namespace App\Entity\Listener;

use App\VersionOne\Message\ImportAllTypesAssetsMessage;
use Symfony\Component\Messenger\MessageBusInterface;

class ProgramIncrementEntityListener
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
