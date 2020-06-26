<?php

namespace App\Command;

use App\VersionOne\AssetMetadata\Asset;
use App\VersionOne\AssetMetadata\Workitem;
use App\VersionOne\Sync\AssetImporter;
use App\VersionOne\Sync\AssetToEntityMap;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ImportCommand extends Command
{
    /**
     * @inheritDoc
     */
    protected static $defaultName = 'import';

    /**
     * @var AssetImporter
     */
    private $importer;

    private $assetTypesToImport = [];

    public function __construct(AssetImporter $importer)
    {
        parent::__construct();

        $this->importer = $importer;
    }

    /**
     * @inheritDoc
     */
    protected function configure(): void
    {
        $this->setDescription('Reflects changes made on entities in VersionOne to entities in the project database.');
    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $this->importAssets(Workitem::class);

        $io->success('The entities have been successfully imported.');

        return 0;
    }

    /**
     * @param string|Asset $assetClassName
     */
    private function importAssets(string $assetClassName): void
    {
        if (in_array($assetClassName, $this->assetTypesToImport, true)) {
            return;
        }
        $this->assetTypesToImport[] = $assetClassName;

        $this->importAssetDependencies($assetClassName);

        echo "Importing {$assetClassName::getType()}..." . PHP_EOL;
        $this->importer->importAssets($assetClassName);
    }

    private function importAssetDependencies($assetClassName): void
    {
        $dependencies = array_keys(
            array_intersect_key(
                $assetClassName::getAttributesToSelect(),
                AssetToEntityMap::MAP
            )
        );
        foreach ($dependencies as $dependencyClassName) {
            $this->importAssets($dependencyClassName);
        }
    }
}
