<?php

namespace App\Integration\Jira\Command;

use App\Integration\Jira\ApiClient;
use App\Integration\Jira\AssetImporter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ImportIssueFieldValuesCommand extends Command
{
    public const NAME = 'jira:import:issue-field-values';
    private const ARGUMENT_PROJECT_KEYS = 'project-keys';
    private const ARGUMENT_ISSUE_TYPE = 'issue-type';
    private const ARGUMENT_FIELD_KEY = 'field-key';
    private const ARGUMENT_ASSET_TYPE = 'asset-type';

    private SymfonyStyle $io;
    private ApiClient $apiClient;
    private AssetImporter $assetImporter;

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
            ->addArgument(self::ARGUMENT_ISSUE_TYPE, InputArgument::REQUIRED)
            ->addArgument(self::ARGUMENT_FIELD_KEY, InputArgument::REQUIRED)
            ->addArgument(self::ARGUMENT_ASSET_TYPE, InputArgument::REQUIRED)
            ->addArgument(self::ARGUMENT_PROJECT_KEYS, InputArgument::REQUIRED | InputArgument::IS_ARRAY);
    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->io = new SymfonyStyle($input, $output);
        $this->io->writeln('Importing...');

        $assets = $this->apiClient->getIssueFieldValues(
            $input->getArgument(self::ARGUMENT_PROJECT_KEYS),
            $input->getArgument(self::ARGUMENT_ISSUE_TYPE),
            $input->getArgument(self::ARGUMENT_FIELD_KEY)
        );
        $this->assetImporter->persistAssets($assets, $input->getArgument(self::ARGUMENT_ASSET_TYPE));

        $this->io->success('Completed');
        return self::SUCCESS;
    }
}
