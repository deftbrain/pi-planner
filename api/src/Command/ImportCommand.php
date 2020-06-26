<?php

namespace App\Command;

use App\VersionOne\AssetMetadata\Asset;
use App\VersionOne\Sync\AssetImporter;
use App\VersionOne\Sync\AssetToEntityMap;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ImportCommand extends Command
{
    private const OPTION_IGNORE_DEPENDENCIES = 'ignore-dependencies';
    private const ARGUMENT_ASSET_TYPE = 'asset-type';

    /**
     * @inheritDoc
     */
    protected static $defaultName = 'app:import-v1-assets';

    /**
     * @var SymfonyStyle
     */
    private $io;

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
        $this->setDescription('Reflects changes made on entities in VersionOne to entities in the project database.')
            ->addArgument(
                self::ARGUMENT_ASSET_TYPE,
                InputArgument::OPTIONAL,
                'An asset type to import. If is not set, assets of all types will be imported. '
                . 'Available values: ' . implode(', ', $this->getAvailableAssetTypes())
            )
            ->addOption(self::OPTION_IGNORE_DEPENDENCIES, 'd');
    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->io = new SymfonyStyle($input, $output);
        $assetMetadataClassName = $input->getArgument(self::ARGUMENT_ASSET_TYPE);
        $assetTypes = $assetMetadataClassName
            ? [(new \ReflectionClass(Asset::class))->getNamespaceName() . '\\' . $assetMetadataClassName,]
            : array_keys(AssetToEntityMap::MAP);

        $mustIgnoreDependencies = $input->getOption(self::OPTION_IGNORE_DEPENDENCIES);
        foreach ($assetTypes as $type) {
            $mustIgnoreDependencies ? $this->importAssets($type) : $this->importAssetsAndDependencies($type);
        }

        $this->io->success('The assets have been successfully imported.');
        return 0;
    }

    /**
     * @param string|Asset $assetClassName
     */
    private function importAssetsAndDependencies(string $assetClassName): void
    {
        if (in_array($assetClassName, $this->assetTypesToImport, true)) {
            return;
        }
        $this->assetTypesToImport[] = $assetClassName;
        $this->importAssetDependencies($assetClassName);
        $this->importAssets($assetClassName);
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
            $this->importAssetsAndDependencies($dependencyClassName);
        }
    }

    /**
     * @param $assetClassName
     */
    private function importAssets($assetClassName): void
    {
        $this->io->writeln(sprintf('Importing %s...', $assetClassName::getType()), OutputInterface::VERBOSITY_VERBOSE);
        $this->importer->importAssets($assetClassName);
    }

    private function getAvailableAssetTypes(): array
    {
        $assetMetadataClasses = array_keys(AssetToEntityMap::MAP);
        return array_map(
            function ($className) {
                return (new \ReflectionClass($className))->getShortName();
            },
            $assetMetadataClasses
        );
    }
}
