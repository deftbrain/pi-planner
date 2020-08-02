<?php

namespace App\MessageHandler;

use App\Command\ImportCommand;
use App\Message\ImportEntitiesMessage;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class ImportEntitiesHandler implements MessageHandlerInterface
{
    /**
     * @var ImportCommand
     */
    private $importCommand;

    public function __construct(ImportCommand $importCommand)
    {
        $this->importCommand = $importCommand;
    }

    public function __invoke(ImportEntitiesMessage $message)
    {
        $this->importCommand->run(new ArrayInput([]), new NullOutput);
    }
}
