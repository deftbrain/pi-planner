<?php

namespace App\Integration\Jira\Command;

use App\Entity\Epic;
use App\Entity\ProjectSettings;
use App\Entity\Workitem;
use App\Integration\Jira\ApiClient;
use App\Integration\Jira\AssetImporter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ImportStoriesCommand extends Command
{
    public const NAME = 'jira:import:stories';
    private const ARGUMENT_ISSUE_TYPES = 'issue-types';
    private const OPTION_FORCE_UPDATE = 'force-update';

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
            'Issue types to take into account (separate multiple keys with a space, use quotes for types with spaces in names)'
        )->addOption(
            self::OPTION_FORCE_UPDATE,
            'f',
            InputOption::VALUE_NONE,
            'Forces updating previously imported workitems. By default, previously imported workitems with'
            . ' the unchanged \'updated\' attribute value in a related Jira issue are ignored.'
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
        $isForceUpdateEnabled = $input->getOption(self::OPTION_FORCE_UPDATE);
        /** @var ProjectSettings[] $projectsSettings */
        $projectsSettings = $this->entityManager->getRepository(ProjectSettings::class)->findAll();
        foreach ($projectsSettings as $projectSettings) {
            $epics = $projectSettings->getEpics()->toArray();
            // We import workitems from selected epics only
            $affectedWorkitems = $this->importEpicWorkitems($epics, $issueTypes, $isForceUpdateEnabled);

            // If a previously imported workitem from the selected epic has been moved in Jira to a different epic
            // not linked to any program increment (actually to project settings) in our system, that workitem will not
            // be updated by the importEpicWorkitems() method and will continue to belong to the original epic
            // in our system. We have to find such workitems by their external IDs and update them in our system.
            $notAffectedWorkitems = $this->getNotAffectedEpicWorkitems($epics, $affectedWorkitems);
            $workitemsMovedToDifferentEpics = $this->importWorkitems($notAffectedWorkitems, $isForceUpdateEnabled);

            // If some workitems have not been found until this step, it means
            // they have been deleted in Jira and we should reflect it in our system
            $this->markWorkitemsAsDeleted(array_diff($notAffectedWorkitems, $workitemsMovedToDifferentEpics));
        }

        $this->io->success('Completed');
        return self::SUCCESS;
    }

    private function importEpicWorkitems(array $epics, array $issueTypes, bool $isForceUpdateEnabled): array
    {
        if (!$epics) {
            return [];
        }

        $processedWorkitems = [];
        $this->apiClient->processPaginatedData(
            static fn (ApiClient $apiClient, int $startAt) => $apiClient->getIssuesFromEpics(
                array_map(static fn (Epic $epic) => $epic->getExternalId(), $epics),
                $issueTypes,
                $startAt
            ),
            function (array $response, int $startAt) use ($isForceUpdateEnabled, &$processedWorkitems) {
                $this->logPaginatedResponse($response, $startAt);
                $this->assetImporter->persistAssets($response['issues'], 'Story', $isForceUpdateEnabled);
                $processedWorkitems[] = array_column($response['issues'], 'key');
            }
        );

        return array_merge(...$processedWorkitems);
    }

    private function getNotAffectedEpicWorkitems(array $epics, array $affectedWorkitems): array
    {
        if (!$epics) {
            return [];
        }

        $result = $this->entityManager
            ->createQueryBuilder()
            ->from(Workitem::class, 'w')
            ->select('w.externalId')
            ->where('IDENTITY(w.epic) IN (:affectedEpics)')
            ->setParameter('affectedEpics', array_map(static fn (Epic $epic) => $epic->getId(), $epics))
            ->andWhere('w.externalId NOT IN (:affectedWorkitems)')
            ->setParameter('affectedWorkitems', $affectedWorkitems)
            ->getQuery()
            ->getScalarResult();

        return array_column($result, 'externalId');
    }

    private function importWorkitems(array $workitems, bool $isForceUpdateEnabled): array
    {
        if (!$workitems) {
            return [];
        }

        $processedWorkitems = [];
        $this->apiClient->processPaginatedData(
            static fn (ApiClient $apiClient, int $startAt) => $apiClient->getIssues($workitems, $startAt),
            function (array $response, int $startAt) use ($isForceUpdateEnabled, &$processedWorkitems) {
                $this->logPaginatedResponse($response, $startAt);
                $this->assetImporter->persistAssets($response['issues'], 'Story', $isForceUpdateEnabled);
                $processedWorkitems[] = array_column($response['issues'], 'key');
            }
        );

        return array_merge(...$processedWorkitems);
    }

    private function markWorkitemsAsDeleted(array $workitems): void
    {
        if (!$workitems) {
            return;
        }

        $workitemsToMarkAsDeleted = $this->entityManager
            ->getRepository(Workitem::class)
            ->findBy(['externalId' => $workitems]);
        foreach ($workitemsToMarkAsDeleted as $workitem) {
            if (!$workitem->getIsDeleted()) {
                // Mark a workitem as deleted via a related object to let entity listeners
                // know about changes and send updates to the client app immediately
                $workitem->setIsDeleted(true);
            }
        }

        $this->entityManager->flush();
    }

    private function logPaginatedResponse(array $response, int $startAt): void
    {
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
}
