<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ApiResource(mercure={"private": true})
 * @ApiFilter(SearchFilter::class, properties={"epic": "exact", "isDeleted": "exact"})
 * @ORM\Entity(repositoryClass="App\Repository\WorkitemRepository")
 */
class Workitem extends AbstractEntity
{
    /**
     * @Assert\NotBlank
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
     * @Assert\NotBlank
     * @ORM\ManyToOne(targetEntity="App\Entity\Epic")
     * @ORM\JoinColumn(nullable=false)
     */
    private $epic;

    /**
     * @Assert\NotBlank
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

    /**
     * @ORM\ManyToMany(targetEntity=Workitem::class, inversedBy="dependants")
     * @ORM\JoinTable(name="workitem_dependencies",
     *      joinColumns={@ORM\JoinColumn(name="workitem_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="dependency_id", referencedColumnName="id")}
     * )
     */
    private $dependencies;

    /**
     * @ORM\ManyToMany(targetEntity=Workitem::class, mappedBy="dependencies")
     */
    private $dependants;

    public function __construct()
    {
        $this->dependencies = new ArrayCollection();
        $this->dependants = new ArrayCollection();
    }

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

    /**
     * @return Collection|self[]
     */
    public function getDependencies(): Collection
    {
        return $this->dependencies;
    }

    public function addDependency(?self $dependency): self
    {
        if ($dependency && !$this->dependencies->contains($dependency)) {
            $this->dependencies[] = $dependency;
        }

        return $this;
    }

    public function removeDependency(self $dependency): self
    {
        if ($this->dependencies->contains($dependency)) {
            $this->dependencies->removeElement($dependency);
        }

        return $this;
    }

    /**
     * @return Collection|self[]
     */
    public function getDependants(): Collection
    {
        return $this->dependants;
    }

    public function addDependant(?self $dependant): self
    {
        if ($dependant && !$this->dependants->contains($dependant)) {
            $this->dependants[] = $dependant;
        }

        return $this;
    }

    public function removeDependant(self $dependant): self
    {
        if ($this->dependants->contains($dependant)) {
            $this->dependants->removeElement($dependant);
        }

        return $this;
    }
}
