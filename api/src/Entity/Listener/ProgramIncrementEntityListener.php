<?php

namespace App\Entity\Listener;

use App\Message\ImportEntitiesMessage;
use Symfony\Component\Messenger\MessageBusInterface;

class ProgramIncrementEntityListener
{
    /**
     * @var MessageBusInterface
     */
    private $bus;

    public function __construct(MessageBusInterface $bus)
    {
        $this->bus = $bus;
    }

    public function importEntities(): void
    {
        $this->bus->dispatch(new ImportEntitiesMessage);
    }
}
