<?php

namespace App\VersionOne\Command;

use App\Entity\AbstractEntity;
use App\VersionOne\AssetMetadata\AssetMetadataFactory;
use App\VersionOne\AssetMetadata\AssetMetadataInterface;
use App\VersionOne\AssetMetadata\BaseAsset\IDAttribute;
use App\VersionOne\Sync\AssetImporter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactoryInterface;

class ImportCommand extends Command
{
    private const ARGUMENT_ASSET_TYPE = 'asset-type';
    private const OPTION_IGNORE_DEPENDENCIES = 'ignore-dependencies';

    /**
     * @var SymfonyStyle
     */
    private $io;

    /**
     * @var AssetImporter
     */
    private $importer;

    private AssetMetadataFactory $assetMetadataFactory;

    private ClassMetadataFactoryInterface $classMetadataFactory;

    private $assetTypesToImport = [];

    public function __construct(
        AssetImporter $importer,
        AssetMetadataFactory $assetMetadataFactory,
        ClassMetadataFactoryInterface $classMetadataFactory
    ) {
        $this->importer = $importer;
        $this->assetMetadataFactory = $assetMetadataFactory;
        $this->classMetadataFactory = $classMetadataFactory;

        parent::__construct();
    }

    /**
     * @inheritDoc
     */
    protected function configure(): void
    {
        $this->setName('version-one:import-assets')
            ->setDescription('Reflects changes made on entities in VersionOne to entities in the project database.')
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
        $assetType = $input->getArgument(self::ARGUMENT_ASSET_TYPE);
        $assetTypes = $assetType ? [$assetType] : $this->getAvailableAssetTypes();
        $assetMetadataSet = $this->assetMetadataFactory->getMetadataFor($assetTypes);
        $mustIgnoreDependencies = $input->getOption(self::OPTION_IGNORE_DEPENDENCIES);
        foreach ($assetMetadataSet as $assetMetadata) {
            $mustIgnoreDependencies
                ? $this->importAssets($assetMetadata)
                : $this->importAssetsAndDependencies($assetMetadata);
        }

        $this->io->success('The assets have been successfully imported.');
        return 0;
    }

    private function importAssetsAndDependencies(AssetMetadataInterface $assetMetadata): void
    {
        if (in_array($assetMetadata->getType(), $this->assetTypesToImport, true)) {
            return;
        }
        $this->assetTypesToImport[] = $assetMetadata->getType();
        $this->importAssetDependencies($assetMetadata);
        $this->importAssets($assetMetadata);
    }

    private function importAssetDependencies(AssetMetadataInterface $assetMetadata): void
    {
        foreach ($assetMetadata->getAttributes() as $attribute) {
            if ($attribute->isRelation() && !$attribute instanceof IDAttribute) {
                $relatedAssetMetadata = $this->assetMetadataFactory->getMetadataFor([$attribute->getRelatedAsset()])[0];
                $this->importAssetsAndDependencies($relatedAssetMetadata);
            }
        }
    }

    private function importAssets(AssetMetadataInterface $assetMetadata): void
    {
        $this->io->writeln(sprintf('Importing %s...', $assetMetadata->getType()), OutputInterface::VERBOSITY_VERBOSE);
        $this->importer->importAssets($assetMetadata);
    }

    /**
     * @return string[]
     */
    private function getAvailableAssetTypes(): array
    {
        return array_keys(
            $this->classMetadataFactory
                ->getMetadataFor(AbstractEntity::class)
                ->getClassDiscriminatorMapping()
                ->getTypesMapping()
        );
    }
}
