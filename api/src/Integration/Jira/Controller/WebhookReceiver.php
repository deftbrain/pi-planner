<?php

namespace App\Integration\Jira\Controller;

use App\Integration\Jira\WebhookHandler;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/webhooks/jira/{issueKey}", methods={"POST"}, name="jira_issue_webhook_receiver")
 */
class WebhookReceiver
{
    private WebhookHandler $webhookHandler;

    public function __construct(WebhookHandler $webhookHandler)
    {
        $this->webhookHandler = $webhookHandler;
    }

    public function __invoke(string $issueKey): JsonResponse
    {
        $this->webhookHandler->handleIssue($issueKey);
        return new JsonResponse();
    }
}
