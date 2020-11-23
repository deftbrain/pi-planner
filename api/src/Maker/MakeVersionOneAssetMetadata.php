<?php

namespace App\Maker;

use App\Entity\AbstractEntity;
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
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactoryInterface;

class MakeVersionOneAssetMetadata extends AbstractMaker
{
    private const ASSET_METADATA_NAMESPACE_PREFIX_TEMPLATE = 'VersionOne\\AssetMetadata\\%s\\';
    private const ARG_ASSET_TYPE = 'asset-type';
    private const BASE_ASSET_TYPE = 'BaseAsset';

    private MetaApiClient $apiClient;
    private ClassMetadataFactoryInterface $classMetadataFactory;
    private Filesystem $filesystem;

    public function __construct(
        MetaApiClient $apiClient,
        ClassMetadataFactoryInterface $classMetadataFactory,
        Filesystem $filesystem
    ) {
        $this->apiClient = $apiClient;
        $this->classMetadataFactory = $classMetadataFactory;
        $this->filesystem = $filesystem;
    }

    public static function getCommandName(): string
    {
        return 'make:version-one:asset-metadata';
    }

    public function configureCommand(Command $command, InputConfiguration $inputConfig): void
    {
        $command->setDescription('Creates classes for the specified VersionOne asset type and its attributes')
            ->addArgument(self::ARG_ASSET_TYPE, InputArgument::REQUIRED, 'What asset type do you want to cover?');
        $inputConfig->setArgumentAsNonInteractive(self::ARG_ASSET_TYPE);
    }

    public function interact(InputInterface $input, ConsoleStyle $io, Command $command): void
    {
        if (!$input->getArgument(self::ARG_ASSET_TYPE)) {
            $question = new Question(
                sprintf(
                    ' <fg=green>%s</>',
                    $command->getDefinition()->getArgument(self::ARG_ASSET_TYPE)->getDescription()
                )
            );
            $question->setAutocompleterValues($this->getAvailableAssetTypes());
            $event = $io->askQuestion($question);
            $input->setArgument(self::ARG_ASSET_TYPE, $event);
        }
    }

    public function generate(InputInterface $input, ConsoleStyle $io, Generator $generator): void
    {
        $assetType = $input->getArgument(self::ARG_ASSET_TYPE);
        $attributeNames = $this->getAssetAttributeNames($assetType);
        $assetMetadata = $this->apiClient->getMetadata($assetType);

        if ($assetType !== self::BASE_ASSET_TYPE) {
            $baseAssetAttributeNames = $this->getAssetAttributeNames(self::BASE_ASSET_TYPE);
            $attributeNames = array_diff($attributeNames, $baseAssetAttributeNames);
        }

        $attributeShortClassNames = [];
        foreach ($attributeNames as $attributeName) {
            $attributeClassNameDetails = $generator->createClassNameDetails(
                $attributeName,
                sprintf(self::ASSET_METADATA_NAMESPACE_PREFIX_TEMPLATE, $assetType),
                'Attribute'
            );
            $attributeShortClassNames[] = $attributeClassNameDetails->getShortName();

            $attributeMetadata = $assetMetadata['Attributes'][$assetType . '.' . $attributeName];
            $attributeClassName = $attributeClassNameDetails->getFullName();
            $this->removeClassFileIfExists($attributeClassName);
            $generator->generateClass(
                $attributeClassName,
                'src/Resources/skeleton/version-one/AssetAttributeMetadata.tpl.php',
                [
                    'name' => $attributeName,
                    'is_multi_value' => $attributeMetadata['IsMultivalue'],
                    'is_relation' => $attributeMetadata['AttributeType'] === 'Relation',
                    'related_asset' => $attributeMetadata['RelatedAsset']['nameref'] ?? null,
                ]
            );
        }

        $assetClassNameDetails = $generator->createClassNameDetails(
            $assetType,
            sprintf(self::ASSET_METADATA_NAMESPACE_PREFIX_TEMPLATE, $assetType),
            'AssetMetadata'
        );
        $assetClassName = $assetClassNameDetails->getFullName();
        $this->removeClassFileIfExists($assetClassName);
        $generator->generateClass(
            $assetClassName,
            'src/Resources/skeleton/version-one/AssetTypeMetadata.tpl.php',
            [
                'asset_type' => $assetType,
                'is_base_type' => $assetType === self::BASE_ASSET_TYPE,
                'attribute_classes' => $attributeShortClassNames,
            ]
        );

        $generator->writeChanges();
        $this->writeSuccessMessage($io);
    }

    public function configureDependencies(DependencyBuilder $dependencies): void
    {
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

    /**
     * @return string[]
     */
    private function getAssetAttributeNames(string $assetType): array
    {
        $entityClassName = $assetType === self::BASE_ASSET_TYPE
            ? AbstractEntity::class
            : $this->getEntityClass($assetType);
        $attributeMetadata = $this->classMetadataFactory
            ->getMetadataFor($entityClassName)
            ->getAttributesMetadata();
        return array_map(fn($a) => $a->getSerializedName(), $attributeMetadata);
    }

    private function getEntityClass(string $assetType): string
    {
        return $this->classMetadataFactory
            ->getMetadataFor(AbstractEntity::class)
            ->getClassDiscriminatorMapping()
            ->getClassForType($assetType);
    }

    private function removeClassFileIfExists(string $className): void
    {
        if (class_exists($className)) {
            $class = new \ReflectionClass($className);
            $this->filesystem->remove($class->getFileName());
        }
    }
}
