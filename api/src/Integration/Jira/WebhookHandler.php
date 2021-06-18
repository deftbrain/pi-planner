<?php

namespace App\Integration\Jira;

use App\Entity\AbstractEntity;
use App\Entity\Epic;
use App\Entity\Workitem;
use Doctrine\ORM\EntityManagerInterface;

class WebhookHandler
{
    private ApiClient $apiClient;
    private AssetImporter $assetImporter;
    private EntityManagerInterface $entityManager;

    public function __construct(
        ApiClient $apiClient,
        AssetImporter $assetImporter,
        EntityManagerInterface $entityManager
    ) {
        $this->apiClient = $apiClient;
        $this->assetImporter = $assetImporter;
        $this->entityManager = $entityManager;
    }

    public function handleIssue(string $issueKey): void
    {
        $issue = $this->apiClient->getIssue($issueKey);
        $criteria = ['externalId' => $issueKey];
        /** @var AbstractEntity|null $entity */
        $entity = $this->entityManager->getRepository(Workitem::class)->findOneBy($criteria)
            ?: $this->entityManager->getRepository(Epic::class)->findOneBy($criteria);

        $isDeletedIssue = $issue === null;
        if ($isDeletedIssue) {
            if ($entity) {
                $entity->setIsDeleted(true);
                $this->entityManager->flush();
            }
            return;
        }

        $isEpic = $issue['fields']['issuetype']['name'] === 'Epic';
        $isNewIssue = !$entity;
        if ($isNewIssue) {
            // A webhook's JQL query contains a white list for projects therefore we don't check it here
            $shouldBeImported = $issue['fields']['status']['statusCategory']['key'] !== 'done';
            if (!$shouldBeImported) {
                return;
            }

            if ($isEpic) {
                // There is no way to get a numeric order value from Jira so set it to 0 for now.
                // The proper value will be set within the next synchronization run via an appropriate cron job.
                // https://community.atlassian.com/t5/Jira-questions/How-do-I-get-the-rank-field-to-return-numeric-values/qaq-p/47214
                $issue['sortOrder'] = 0;
            }
        }

        $this->assetImporter->persistAssets([$issue], $isEpic ? 'Epic' : 'Story');
    }
}
