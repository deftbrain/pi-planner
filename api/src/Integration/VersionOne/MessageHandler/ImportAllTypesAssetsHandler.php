<?php

namespace App\Integration\VersionOne\MessageHandler;

use App\Integration\VersionOne\Command\ImportCommand;
use App\Integration\VersionOne\Message\ImportAllTypesAssetsMessage;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class ImportAllTypesAssetsHandler implements MessageHandlerInterface
{
    private ImportCommand $importCommand;

    public function __construct(ImportCommand $importCommand)
    {
        $this->importCommand = $importCommand;
    }

    public function __invoke(ImportAllTypesAssetsMessage $message)
    {
        $this->importCommand->run(new ArrayInput([]), new NullOutput);
    }
}
