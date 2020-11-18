<?php

namespace App\Maker;

use App\VersionOne\MetaApiClient;
use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\DependencyBuilder;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Bundle\MakerBundle\InputConfiguration;
use Symfony\Bundle\MakerBundle\Maker\AbstractMaker;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactoryInterface;
use Symfony\Component\Yaml\Yaml;

class MakeVersionOneAssetMetadata extends AbstractMaker
{
    private const ASSET_METADATA_NAMESPACE_PREFIX_TEMPLATE = 'VersionOne\\AssetMetadata\\%s\\';
    private const ARG_ASSET = 'asset';

    private MetaApiClient $apiClient;
    private ClassMetadataFactoryInterface $metadataFactory;
    private array $assetTypeToEntityClassMap;

    public function __construct(
        MetaApiClient $apiClient,
        ClassMetadataFactoryInterface $metadataFactory,
        string $assetToEntityMapConfigPath
    ) {
        $this->apiClient = $apiClient;
        $this->metadataFactory = $metadataFactory;
        $this->assetTypeToEntityClassMap = Yaml::parse(file_get_contents($assetToEntityMapConfigPath));
    }

    public static function getCommandName(): string
    {
        return 'make:version-one:asset-metadata';
    }

    public function configureCommand(Command $command, InputConfiguration $inputConfig): void
    {
        $command
            ->setDescription('Creates classes for the specified VersionOne asset type and its attributes')
            ->addArgument(self::ARG_ASSET, InputArgument::REQUIRED, 'What asset type do you want to cover?')
            ->setHelp(<<<TEXT
                Uses version_one_asset_map.yml and version_one_serializer.yml
                for getting lists of available asset types and their attributes.
            TEXT
            );
        $inputConfig->setArgumentAsNonInteractive(self::ARG_ASSET);
    }

    public function interact(InputInterface $input, ConsoleStyle $io, Command $command): void
    {
        if (!$input->getArgument(self::ARG_ASSET)) {
            $question = new Question(
                sprintf(
                    ' <fg=green>%s</>',
                    $command->getDefinition()->getArgument(self::ARG_ASSET)->getDescription()
                )
            );
            $question->setAutocompleterValues(array_keys($this->assetTypeToEntityClassMap));
            $event = $io->askQuestion($question);
            $input->setArgument(self::ARG_ASSET, $event);
        }
    }

    public function generate(InputInterface $input, ConsoleStyle $io, Generator $generator): void
    {
        $asset = $input->getArgument(self::ARG_ASSET);
        $entityShortClassName = $this->assetTypeToEntityClassMap[$asset];
        $entityClassNameDetails = $generator->createClassNameDetails($entityShortClassName, 'Entity\\');
        $entityPropertiesMetadata = $this->metadataFactory->getMetadataFor($entityClassNameDetails->getFullName())->getAttributesMetadata();
        $assetMetadata = $this->apiClient->getMetadata($asset);
        $attributeShortClassNames = [];
        foreach ($entityPropertiesMetadata as $propertyMetadata) {
            $attributeName = $propertyMetadata->getSerializedName();
            $attributeClassNameDetails = $generator->createClassNameDetails(
                $attributeName,
                sprintf(self::ASSET_METADATA_NAMESPACE_PREFIX_TEMPLATE, $asset),
                'Attribute'
            );
            $attributeShortClassNames[] = $attributeClassNameDetails->getShortName();

            $attributeMetadata = $assetMetadata['Attributes'][$asset . '.' . $attributeName];
            $generator->generateClass(
                $attributeClassNameDetails->getFullName(),
                'src/Resources/skeleton/version-one/AssetAttributeMetadata.tpl.php',
                [
                    'name' => $attributeName,
                    'is_read_only' => $attributeMetadata['IsReadOnly'],
                    'is_multi_value' => $attributeMetadata['IsMultivalue'],
                    'is_relation' => $attributeMetadata['AttributeType'] === 'Relation',
                    'related_asset' => $attributeMetadata['RelatedAsset']['nameref'] ?? null,
                ]
            );
        }

        $assetClassNameDetails = $generator->createClassNameDetails(
            $asset,
            sprintf(self::ASSET_METADATA_NAMESPACE_PREFIX_TEMPLATE, $asset),
            'Asset'
        );
        $generator->generateClass(
            $assetClassNameDetails->getFullName(),
            'src/Resources/skeleton/version-one/AssetMetadata.tpl.php',
            ['attribute_classes' => $attributeShortClassNames]
        );

        $generator->writeChanges();
        $this->writeSuccessMessage($io);
    }

    public function configureDependencies(DependencyBuilder $dependencies): void
    {
    }
}
