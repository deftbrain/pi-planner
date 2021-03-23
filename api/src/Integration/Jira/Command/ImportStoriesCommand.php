<?php

namespace App\Integration\Jira\Command;

use App\Entity\Epic;
use App\Entity\ProjectSettings;
use App\Integration\Jira\ApiClient;
use App\Integration\Jira\AssetImporter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ImportStoriesCommand extends Command
{
    public const NAME = 'jira:import:stories';
    private const ARGUMENT_ISSUE_TYPES = 'issue-types';

    private SymfonyStyle $io;
    private ApiClient $apiClient;
    private AssetImporter $assetImporter;
    private EntityManagerInterface $entityManager;

    public function __construct(
        ApiClient $apiClient,
        AssetImporter $assetImporter,
        EntityManagerInterface $entityManager
    ) {
        parent::__construct();
        $this->apiClient = $apiClient;
        $this->assetImporter = $assetImporter;
        $this->entityManager = $entityManager;
    }

    /**
     * @inheritDoc
     */
    protected function configure(): void
    {
        $this->setName(self::NAME)->addArgument(
            self::ARGUMENT_ISSUE_TYPES,
            InputArgument::REQUIRED | InputArgument::IS_ARRAY,
            'Issue types (separate multiple keys with a space, use quotes for types with spaces)'
        );
    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->io = new SymfonyStyle($input, $output);
        $this->io->writeln('Importing...');

        $issueTypes = $input->getArgument(self::ARGUMENT_ISSUE_TYPES);
        /** @var ProjectSettings[] $projectsSettings */
        $projectsSettings = $this->entityManager->getRepository(ProjectSettings::class)->findAll();
        foreach ($projectsSettings as $projectSettings) {
            $epicExternalIds = array_map(
                static fn (Epic $epic) => $epic->getExternalId(),
                $projectSettings->getEpics()->toArray()
            );
            if ($epicExternalIds) {
                $this->apiClient->processPaginatedData(
                    static fn (ApiClient $apiClient, int $startAt) => $apiClient->getIssues(
                        $epicExternalIds,
                        $issueTypes,
                        $startAt
                    ),
                    function (array $response, int $startAt) {
                        $this->assetImporter->persistAssets($response['issues'], 'Story');
                        $this->io->writeln(
                            sprintf(
                                'startAt: %d. maxResults: %d. total: %d',
                                $startAt,
                                $response['maxResults'],
                                $response['total'] ?? 0
                            ),
                            $this->io::VERBOSITY_VERBOSE
                        );
                    }
                );
            }
        }

        $this->io->success('Completed');
        return self::SUCCESS;
    }
}
