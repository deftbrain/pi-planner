<?php

namespace App\Integration\Jira;

use App\Integration\AssetExternalUrlProviderInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class IssueDetailsUrlProvider implements AssetExternalUrlProviderInterface
{
    private ParameterBagInterface $params;

    public function __construct(ParameterBagInterface $params)
    {
        $this->params = $params;
    }

    public function getUrl(string $externalId): string
    {
        return sprintf(
            '%s%s/%s',
            $this->params->get('jira.server_base_uri'),
            $this->params->get('jira.endpoint.browse'),
            $externalId
        );
    }
}
