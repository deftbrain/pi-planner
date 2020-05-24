<?php

namespace App\Command;

use App\VersionOne\AssetMetadata\BacklogGroup;
use App\VersionOne\AssetMetadata\Epic;
use App\VersionOne\AssetMetadata\EpicStatus;
use App\VersionOne\AssetMetadata\Project;
use App\VersionOne\AssetMetadata\Sprint;
use App\VersionOne\AssetMetadata\Team;
use App\VersionOne\AssetMetadata\Workitem;
use App\VersionOne\Sync\Synchronizer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class SyncCommand extends Command
{
    private const EAGER_SYNC_ASSETS = [
        BacklogGroup::class,
        EpicStatus::class,
        Project::class,
        Sprint::class,
        Team::class,
        Epic::class,
        Workitem::class,
    ];

    /**
     * @inheritDoc
     */
    protected static $defaultName = 'sync';

    /**
     * @var Synchronizer
     */
    private $synchronizer;

    public function __construct(Synchronizer $synchronizer)
    {
        parent::__construct();

        $this->synchronizer = $synchronizer;
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

        foreach (self::EAGER_SYNC_ASSETS as $className) {
            $io->note("Synchronizing $className...");
            $this->synchronizer->syncAssets($className);
        }

        $io->success('The entities have been successfully synchronized.');

        return 0;
    }
}
