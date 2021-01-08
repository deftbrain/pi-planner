<?php

namespace App\VersionOne\Command;

use App\Entity\AbstractEntity;
use App\VersionOne\AssetMetadata\AssetMetadataFactory;
use App\VersionOne\AssetMetadata\BaseAsset\IDAttribute;
use App\VersionOne\Message\ImportAssetsMessage;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactoryInterface;

class ImportCommand extends Command
{
    public const NAME = 'version-one:import-assets';
    private const ARGUMENT_ASSET_TYPE = 'asset-type';
    private const OPTION_IGNORE_RELATED_ASSETS = 'ignore-related-assets';
    private const OPTION_FORCE_UPDATE = 'force-update';

    private SymfonyStyle $io;
    private AssetMetadataFactory $assetMetadataFactory;
    private ClassMetadataFactoryInterface $classMetadataFactory;
    private MessageBusInterface $messageBus;
    private array $queuedAssetTypes = [];
    private bool $ignoreRelatedAssets;
    private bool $isForceUpdateRequired;

    public function __construct(
        AssetMetadataFactory $assetMetadataFactory,
        ClassMetadataFactoryInterface $classMetadataFactory,
        MessageBusInterface $messageBus
    ) {
        $this->assetMetadataFactory = $assetMetadataFactory;
        $this->classMetadataFactory = $classMetadataFactory;
        $this->messageBus = $messageBus;

        parent::__construct();
    }

    /**
     * @inheritDoc
     */
    protected function configure(): void
    {
        $this->setName(self::NAME)
            ->setDescription('Reflects changes made on assets in VersionOne to entities in the project database.')
            ->addArgument(
                self::ARGUMENT_ASSET_TYPE,
                InputArgument::OPTIONAL,
                'An asset type to import. Available values: ' . implode(', ', $this->getAvailableAssetTypes())
            )
            ->addOption(self::OPTION_IGNORE_RELATED_ASSETS, 'i')
            ->addOption(
                self::OPTION_FORCE_UPDATE,
                'f',
                null,
                'Forces updating existing assets. By default assets with the same'
                . ' ChangeDateUTC attribute value are not updated.'
            );
    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->io = new SymfonyStyle($input, $output);
        $this->ignoreRelatedAssets = $input->getOption(self::OPTION_IGNORE_RELATED_ASSETS);
        $this->isForceUpdateRequired = $input->getOption(self::OPTION_FORCE_UPDATE);
        $assetType = $input->getArgument(self::ARGUMENT_ASSET_TYPE);
        $assetTypes = $assetType ? [$assetType] : $this->getAvailableAssetTypes();
        $this->io->writeln('Importing...');
        foreach ($assetTypes as $assetType) {
            $this->import($assetType);
        }
        $this->io->success('Importing of assets has been scheduled.');
        return 0;
    }

    private function import(string $assetType): void
    {
        if (in_array($assetType, $this->queuedAssetTypes, true)) {
            return;
        }
        $this->queuedAssetTypes[] = $assetType;
        if (!$this->ignoreRelatedAssets) {
            $this->importRelatedAssets($assetType);
        }
        $this->importAssets($assetType);
    }

    private function importRelatedAssets(string $assetType): void
    {
        $assetMetadata = $this->assetMetadataFactory->makeMetadataFor($assetType);
        foreach ($assetMetadata->getAttributes() as $attribute) {
            if (
                $attribute->isRelation()
                // In VersionOne the ID attribute is a relation to the asset itself
                && !$attribute instanceof IDAttribute
                && $attribute->getRelatedAsset() !== $assetType
            ) {
                $this->import($attribute->getRelatedAsset());
            }
        }
    }

    private function importAssets(string $assetType): void
    {
        $this->messageBus->dispatch(new ImportAssetsMessage($assetType, $this->isForceUpdateRequired));
        $this->io->writeln(
            sprintf('Importing of %s assets has been scheduled', $assetType), OutputInterface::VERBOSITY_VERBOSE
        );
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
