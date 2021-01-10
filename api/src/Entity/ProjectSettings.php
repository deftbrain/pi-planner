<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ApiResource()
 * @ApiFilter(SearchFilter::class, properties={"programIncrement": "exact"})
 * @ORM\Entity(repositoryClass="App\Repository\ProjectSettingsRepository")
 */
class ProjectSettings
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Assert\NotBlank
     * @ORM\ManyToOne(targetEntity=Project::class)
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"programIncrement"})
     */
    private $project;

    /**
     * @Assert\NotBlank
     * @ORM\ManyToMany(targetEntity=Sprint::class)
     * @Groups({"programIncrement"})
     */
    private $sprints;

    /**
     * @ORM\OneToMany(targetEntity=TeamSprintCapacity::class, mappedBy="projectSettings", orphanRemoval=true)
     * @Groups({"programIncrement"})
     */
    private $teamSprintCapacities;

    /**
     * @ORM\OneToMany(targetEntity=Epic::class, mappedBy="projectSettings")
     * @Groups({"programIncrement"})
     */
    private $epics;

    /**
     * @ORM\ManyToOne(targetEntity=ProgramIncrement::class, inversedBy="projectsSettings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $programIncrement;

    /**
     * @ORM\ManyToOne(targetEntity=WorkitemStatus::class)
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"programIncrement"})
     */
    private $defaultWorkitemStatus;

    public function __construct()
    {
        $this->sprints = new ArrayCollection();
        $this->teamSprintCapacities = new ArrayCollection();
        $this->epics = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * @return Collection|Sprint[]
     */
    public function getSprints(): Collection
    {
        return $this->sprints;
    }

    public function addSprint(Sprint $sprint): self
    {
        if (!$this->sprints->contains($sprint)) {
            $this->sprints[] = $sprint;
        }

        return $this;
    }

    public function removeSprint(Sprint $sprint): self
    {
        if ($this->sprints->contains($sprint)) {
            $this->sprints->removeElement($sprint);
        }

        return $this;
    }

    /**
     * @return Collection|TeamSprintCapacity[]
     */
    public function getTeamSprintCapacities(): Collection
    {
        return $this->teamSprintCapacities;
    }

    public function addTeamSprintCapacity(TeamSprintCapacity $teamSprintCapacity): self
    {
        if (!$this->teamSprintCapacities->contains($teamSprintCapacity)) {
            $this->teamSprintCapacities[] = $teamSprintCapacity;
            $teamSprintCapacity->setProjectSettings($this);
        }

        return $this;
    }

    public function removeTeamSprintCapacity(TeamSprintCapacity $teamSprintCapacity): self
    {
        if ($this->teamSprintCapacities->contains($teamSprintCapacity)) {
            $this->teamSprintCapacities->removeElement($teamSprintCapacity);
            // set the owning side to null (unless already changed)
            if ($teamSprintCapacity->getProjectSettings() === $this) {
                $teamSprintCapacity->setProjectSettings(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Epic[]
     */
    public function getEpics(): Collection
    {
        return $this->epics;
    }

    public function addEpic(Epic $epic): self
    {
        if (!$this->epics->contains($epic)) {
            $this->epics[] = $epic;
            $epic->setProjectSettings($this);
        }

        return $this;
    }

    public function removeEpic(Epic $epic): self
    {
        if ($this->epics->contains($epic)) {
            $this->epics->removeElement($epic);
            // set the owning side to null (unless already changed)
            if ($epic->getProjectSettings() === $this) {
                $epic->setProjectSettings(null);
            }
        }

        return $this;
    }

    public function getProgramIncrement(): ?ProgramIncrement
    {
        return $this->programIncrement;
    }

    public function setProgramIncrement(?ProgramIncrement $programIncrement): self
    {
        $this->programIncrement = $programIncrement;

        return $this;
    }

    public function getDefaultWorkitemStatus(): ?WorkitemStatus
    {
        return $this->defaultWorkitemStatus;
    }

    public function setDefaultWorkitemStatus(?WorkitemStatus $defaultWorkitemStatus): self
    {
        $this->defaultWorkitemStatus = $defaultWorkitemStatus;

        return $this;
    }
}
