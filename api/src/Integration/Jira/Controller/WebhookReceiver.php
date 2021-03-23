<?php

namespace App\Integration\Jira\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/webhooks/jira/{issueKey}", methods={"POST"}, name="jira_issue_webhook_receiver")
 */
class WebhookReceiver
{
    public function __invoke(string $issueKey): JsonResponse
    {
        // TODO: Implement method
        return new JsonResponse($issueKey);
    }
}
