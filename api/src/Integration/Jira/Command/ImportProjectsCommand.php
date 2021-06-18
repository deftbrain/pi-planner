<?php

namespace App\Integration\Jira\Command;

use App\Integration\Jira\ApiClient;
use App\Integration\Jira\AssetImporter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ImportProjectsCommand extends Command
{
    public const NAME = 'jira:import:projects';
    private const ARGUMENT_PROJECTS = 'projects';

    private SymfonyStyle $io;
    private ApiClient $apiClient;
    private AssetImporter $assetImporter;
    private EntityManagerInterface $entityManager;

    public function __construct(ApiClient $apiClient, AssetImporter $assetImporter)
    {
        parent::__construct();
        $this->apiClient = $apiClient;
        $this->assetImporter = $assetImporter;
    }

    /**
     * @inheritDoc
     */
    protected function configure(): void
    {
        $this->setName(self::NAME)
            ->addArgument(
                self::ARGUMENT_PROJECTS,
                InputArgument::REQUIRED | InputArgument::IS_ARRAY,
                'Projects\' keys (separate multiple keys with a space)'
            );
    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->io = new SymfonyStyle($input, $output);
        $this->io->writeln('Importing...');
        $projects = $this->apiClient->getProjects($input->getArgument(self::ARGUMENT_PROJECTS));
        $this->assetImporter->persistAssets($projects, 'Project');
        $this->io->success('Completed');
        return self::SUCCESS;
    }
}
