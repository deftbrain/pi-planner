<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource()
 * @ORM\Entity(repositoryClass="App\Repository\ProjectRepository")
 */
class Project extends AbstractEntity
{
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\SprintSchedule")
     * @ORM\JoinColumn(nullable=false)
     */
    private $sprintSchedule;

    public function getSprintSchedule(): ?SprintSchedule
    {
        return $this->sprintSchedule;
    }

    public function setSprintSchedule(?SprintSchedule $sprintSchedule): self
    {
        $this->sprintSchedule = $sprintSchedule;

        return $this;
    }
}
