<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource(attributes={"order"={"name": "DESC"}})
 * @ApiFilter(SearchFilter::class, properties={"id": "exact"})
 * @ORM\Entity(repositoryClass="App\Repository\ProjectRepository")
 */
class Project extends AbstractEntity
{
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\SprintSchedule", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
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
