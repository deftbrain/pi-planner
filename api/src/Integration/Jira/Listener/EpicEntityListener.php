<?php

namespace App\Integration\Jira\Listener;

use App\Entity\Epic;

class EpicEntityListener
{
    public function prePersist(Epic $epic): void
    {
    }

    public function preUpdate(Epic $epic): void
    {
    }
}
