<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource()
 * @ORM\Entity(repositoryClass="App\Repository\WorkitemRepository")
 */
class Workitem extends AbstractEntity
{
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Project")
     * @ORM\JoinColumn(nullable=false)
     */
    private $project;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Team")
     */
    private $team;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Sprint")
     */
    private $sprint;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Epic")
     * @ORM\JoinColumn(nullable=false)
     */
    private $epic;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\BacklogGroup")
     * @ORM\JoinColumn(nullable=false)
     */
    private $backlogGroup;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $estimateFrontend;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $estimateBackend;

    public function getProject(): ?Project
    {
        return $this->project;
    }

    public function setProject(?Project $project): self
    {
        $this->project = $project;

        return $this;
    }

    public function getTeam(): ?Team
    {
        return $this->team;
    }

    public function setTeam(?Team $team): self
    {
        $this->team = $team;

        return $this;
    }

    public function getSprint(): ?Sprint
    {
        return $this->sprint;
    }

    public function setSprint(?Sprint $sprint): self
    {
        $this->sprint = $sprint;

        return $this;
    }

    public function getEpic(): ?Epic
    {
        return $this->epic;
    }

    public function setEpic(?Epic $epic): self
    {
        $this->epic = $epic;

        return $this;
    }

    public function getBacklogGroup(): ?BacklogGroup
    {
        return $this->backlogGroup;
    }

    public function setBacklogGroup(?BacklogGroup $backlogGroup): self
    {
        $this->backlogGroup = $backlogGroup;

        return $this;
    }

    public function getEstimateFrontend(): ?float
    {
        return $this->estimateFrontend;
    }

    public function setEstimateFrontend(?float $estimateFrontend): self
    {
        $this->estimateFrontend = $estimateFrontend;

        return $this;
    }

    public function getEstimateBackend(): ?float
    {
        return $this->estimateBackend;
    }

    public function setEstimateBackend(?float $estimateBackend): self
    {
        $this->estimateBackend = $estimateBackend;

        return $this;
    }
}
