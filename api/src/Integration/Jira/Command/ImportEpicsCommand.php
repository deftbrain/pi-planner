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

class ImportEpicsCommand extends Command
{
    public const NAME = 'jira:import:epics';

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

        $projectKeys = $this->entityManager
            ->createQueryBuilder()
            ->from(Project::class, 'p')
            ->select('p.externalId')
            ->getQuery()
            ->getScalarResult();
        $projectKeys = array_column($projectKeys, 'externalId');

        $this->apiClient->processPaginatedData(
            static fn (ApiClient $apiClient, int $startAt) => $apiClient->getEpics($projectKeys, $startAt),
            function (array $response, int $startAt) {
                // There is no way to get a numeric order value from Jira so use the $startAt + $index for that purpose
                // https://community.atlassian.com/t5/Jira-questions/How-do-I-get-the-rank-field-to-return-numeric-values/qaq-p/47214
                array_walk($response['issues'], static fn (&$epic, $index) => $epic['sortOrder'] = $startAt + $index);
                $this->assetImporter->persistAssets($response['issues'], 'Epic');
                $this->io->writeln(
                    sprintf('startAt: %d. maxResults: %d. total: %d', $startAt, $response['maxResults'], $response['total'] ?? 0),
                    $this->io::VERBOSITY_VERBOSE
                );
            }
        );

        $this->io->success('Completed');
        return self::SUCCESS;
    }
}
