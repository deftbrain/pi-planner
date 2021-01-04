<?php

namespace App\Entity\Listener;

use App\Entity\ProjectSettings;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class ProjectSettingsEntityListener
{
    public function preRemove(ProjectSettings $projectSettings, LifecycleEventArgs $event): void
    {
        $objectManager = $event->getObjectManager();
        foreach ($projectSettings->getEpics() as $epic) {
            // An epic owns the association that should be removed manually
            // before removing a related project settings object.
            // Doctrine can't handle that case: https://stackoverflow.com/a/24975434/4664724
            $epic->setProjectSettings(null);
            $objectManager->persist($epic);
        }
    }
}
