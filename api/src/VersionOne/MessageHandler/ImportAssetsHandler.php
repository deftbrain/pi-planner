<?php

namespace App\VersionOne\MessageHandler;

use App\VersionOne\AssetMetadata\AssetMetadataFactory;
use App\VersionOne\Message\ImportAssetsMessage;
use App\VersionOne\Sync\AssetImporter\AssetImporterFactory;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class ImportAssetsHandler implements MessageHandlerInterface
{
    private AssetMetadataFactory $assetMetadataFactory;
    private AssetImporterFactory $assetImporterFactory;

    public function __construct(AssetMetadataFactory $assetMetadataFactory, AssetImporterFactory $assetImporterFactory)
    {
        $this->assetMetadataFactory = $assetMetadataFactory;
        $this->assetImporterFactory = $assetImporterFactory;
    }

    public function __invoke(ImportAssetsMessage $message)
    {
        $assetMetadata = $this->assetMetadataFactory->makeMetadataFor($message->getAssetType());
        $this->assetImporterFactory->makeImporter($assetMetadata)->import();
    }
}
