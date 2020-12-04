<?php

namespace App\VersionOne\MessageHandler;

use App\VersionOne\Message\SetSameTypeRelationsMessage;
use App\VersionOne\Sync\AssetImporter\AssetImporter;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class SetSameTypeRelationsHandler implements MessageHandlerInterface
{
    private AssetImporter $assetImporter;

    public function __construct(AssetImporter $assetImporter)
    {
        $this->assetImporter = $assetImporter;
    }

    public function __invoke(SetSameTypeRelationsMessage $message)
    {
        $this->assetImporter->persistAsset($message->getAsset(), $message->getEntityClassName());
    }
}
