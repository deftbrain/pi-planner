<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource(attributes={"order": {"sortOrder"}})
 * @ApiFilter(SearchFilter::class, properties={"projects": "exact"})
 * @ORM\Entity(repositoryClass="App\Repository\BacklogGroupRepository")
 */
class BacklogGroup extends AbstractEntity
{
    /**
     * @ORM\ManyToMany(targetEntity=Project::class)
     */
    private $projects;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $sortOrder;

    public function __construct()
    {
        $this->projects = new ArrayCollection();
    }

    /**
     * @return Collection|Project[]
     */
    public function getProjects(): Collection
    {
        return $this->projects;
    }

    public function addProject(?Project $project): self
    {
        if ($project && !$this->projects->contains($project)) {
            $this->projects[] = $project;
        }

        return $this;
    }

    public function removeProject(Project $project): self
    {
        $this->projects->removeElement($project);

        return $this;
    }

    public function getSortOrder(): ?int
    {
        return $this->sortOrder;
    }

    public function setSortOrder(?int $sortOrder): self
    {
        $this->sortOrder = $sortOrder;

        return $this;
    }
}
