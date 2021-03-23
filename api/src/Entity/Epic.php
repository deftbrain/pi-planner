<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource(attributes={"order"={"sortOrder"}})
 * @ApiFilter(SearchFilter::class, properties={
 *     "id": "exact",
 *     "project": "exact",
 *     "projectSettings": "exact",
 *     "programIncrement": "exact"
 * })
 * @ApiFilter(OrderFilter::class, properties={"wsjf": {"nulls_comparison": OrderFilter::NULLS_SMALLEST}})
 * @ORM\Entity(repositoryClass="App\Repository\EpicRepository")
 */
class Epic extends AbstractEntity
{
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\EpicStatus")
     * @ORM\JoinColumn(nullable=true)
     */
    private $status;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Project")
     * @ORM\JoinColumn(nullable=false)
     */
    private $project;

    /**
     * @ORM\Column(type="float", length=255, nullable=true)
     */
    private $wsjf;

    /**
     * @ORM\Column(type="integer")
     */
    private $sortOrder;

    /**
     * @ORM\ManyToMany(targetEntity=Team::class)
     */
    private $teams;

    /**
     * @ORM\ManyToOne(targetEntity=ProjectSettings::class, inversedBy="epics")
     */
    private $projectSettings;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity=ProgramIncrement::class)
     */
    private $programIncrement;

    public function __construct()
    {
        $this->teams = new ArrayCollection();
    }

    public function getStatus(): ?EpicStatus
    {
        return $this->status;
    }

    public function setStatus(?EpicStatus $status): self
    {
        $this->status = $status;

        return $this;
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

    public function getWsjf(): ?float
    {
        return $this->wsjf;
    }

    public function setWsjf(?float $wsjf): self
    {
        $this->wsjf = $wsjf;

        return $this;
    }

    public function getSortOrder(): ?int
    {
        return $this->sortOrder;
    }

    public function setSortOrder(int $sortOrder): self
    {
        $this->sortOrder = $sortOrder;

        return $this;
    }

    /**
     * @return Collection|Team[]
     */
    public function getTeams(): Collection
    {
        return $this->teams;
    }

    public function addTeam(Team $team): self
    {
        if (!$this->teams->contains($team)) {
            $this->teams[] = $team;
        }

        return $this;
    }

    public function removeTeam(Team $team): self
    {
        if ($this->teams->contains($team)) {
            $this->teams->removeElement($team);
        }

        return $this;
    }

    public function getProjectSettings(): ?ProjectSettings
    {
        return $this->projectSettings;
    }

    public function setProjectSettings(?ProjectSettings $projectSettings): self
    {
        $this->projectSettings = $projectSettings;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

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
}
