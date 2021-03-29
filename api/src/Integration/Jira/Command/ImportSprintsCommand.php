<?php

namespace App\Integration\Jira\Command;

use App\Entity\Project;
use App\Integration\Jira\ApiClient;
use App\Integration\Jira\AssetImporter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ImportSprintsCommand extends Command
{
    public const NAME = 'jira:import:sprints';

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
        $this->setName(self::NAME);
    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->io = new SymfonyStyle($input, $output);
        $this->io->writeln('Importing...');

        /** @var Project[] $projects */
        $projects = $this->entityManager->getRepository(Project::class)->findAll();
        foreach ($projects as $project) {
            $boardIdsChunks = [];
            $this->apiClient->processPaginatedData(
                static fn (
                    ApiClient $apiClient,
                    int $startAt
                ) => $apiClient->getBoards($project->getExternalId(), $startAt),
                static function (array $response) use (&$boardIdsChunks) {
                    $boardIdsChunks[] = array_column($response['values'], 'id');
                }
            );
            $boardIds = array_merge(...$boardIdsChunks);

            $sprints = [];
            foreach ($boardIds as $boardId) {
                $this->io->writeln(sprintf('Board #%d', $boardId), $this->io::VERBOSITY_VERBOSE);
                $this->apiClient->processPaginatedData(
                    static fn (ApiClient $apiClient, int $startAt) => $apiClient->getSprints($boardId, $startAt),
                    function (array $response, int $startAt) use (&$sprints, $boardId) {
                        $sprints += array_column($response['values'], null, 'id');
                        $this->io->writeln(
                            sprintf(
                                'startAt: %d. maxResults: %d. total: %s',
                                $startAt,
                                $response['maxResults'],
                                $response['total'] ?? 'unknown'
                            ),
                            $this->io::VERBOSITY_VERBOSE
                        );
                        $this->io->writeln(
                            sprintf('Board ID: %d. Sprints: %s', $boardId, json_encode($response['values'])),
                            $this->io::VERBOSITY_VERY_VERBOSE
                        );
                    }
                );
            }
            if (!$sprints) {
                continue;
            }

            $fakeSprintScheduleExternalId = $project->getSprintSchedule()->getExternalId();
            array_walk($sprints, static function (&$sprint) use ($fakeSprintScheduleExternalId) {
                // Add a board ID to a name to be able to be able to distinguish
                // sprints with the same names but from different boards
                $sprint['name'] = sprintf('(%s) %s', $sprint['name'], $sprint['originBoardId']);
                // Link a sprint to a fake sprint schedule to prevent extra changes in code
                $sprint['schedule'] = ['id' => $fakeSprintScheduleExternalId];
            });
            $this->assetImporter->persistAssets($sprints, 'Sprint');
        }

        $this->io->success('Completed');
        return self::SUCCESS;
    }

}
