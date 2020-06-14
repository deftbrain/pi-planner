<?php

namespace App\Command;

use App\VersionOne\AssetMetadata\BacklogGroup;
use App\VersionOne\AssetMetadata\Epic;
use App\VersionOne\AssetMetadata\EpicStatus;
use App\VersionOne\AssetMetadata\Project;
use App\VersionOne\AssetMetadata\Sprint;
use App\VersionOne\AssetMetadata\Team;
use App\VersionOne\AssetMetadata\Workitem;
use App\VersionOne\Sync\AssetImporter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ImportCommand extends Command
{
    private const ASSETS = [
        BacklogGroup::class,
        Epic::class,
        EpicStatus::class,
        Project::class,
        Sprint::class,
        Team::class,
        Workitem::class,
    ];

    /**
     * @inheritDoc
     */
    protected static $defaultName = 'import';

    /**
     * @var AssetImporter
     */
    private $importer;

    public function __construct(AssetImporter $importer)
    {
        parent::__construct();

        $this->importer = $importer;
    }

    /**
     * @inheritDoc
     */
    protected function configure(): void
    {
        $this->setDescription('Reflects changes made on entities in VersionOne to entities in the project database.');
    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        foreach (self::ASSETS as $className) {
            $io->note("Importing $className...");
            $this->importer->importAssets($className);
        }

        $io->success('The entities have been successfully imported.');

        return 0;
    }
}
