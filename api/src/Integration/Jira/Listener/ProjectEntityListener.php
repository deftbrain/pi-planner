<?php

namespace App\Integration\Jira\Listener;

use App\Entity\Project;
use App\Entity\SprintSchedule;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class ProjectEntityListener
{
    public function prePersist(Project $project): void
    {
        $externalId = $project->getExternalId();
        if (!$externalId) {
            return;
        }

        $fakeSprintSchedule = new SprintSchedule();
        $fakeSprintSchedule->setName("Fake schedule for {$externalId} project");
        $fakeSprintSchedule->setExternalId('FAKE-' . $externalId);
        $project->setSprintSchedule($fakeSprintSchedule);
    }
}
