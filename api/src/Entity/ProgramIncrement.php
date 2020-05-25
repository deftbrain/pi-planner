<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource()
 * @ORM\Entity(repositoryClass="App\Repository\ProgramIncrementRepository")
 */
class ProgramIncrement
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ApiProperty(iri="http://schema.org/name")
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Project")
     * @ORM\JoinTable(
     *      name="program_increment_projects",
     *      joinColumns={@ORM\JoinColumn(name="pi_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="project_id", referencedColumnName="id")}
     * )
     */
    private $projects;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Team")
     * @ORM\JoinTable(
     *      name="program_increment_team",
     *      joinColumns={@ORM\JoinColumn(name="pi_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="team_id", referencedColumnName="id")}
     * )
     */
    private $teams;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Sprint")
     * @ORM\JoinTable(
     *      name="program_increment_sprint",
     *      joinColumns={@ORM\JoinColumn(name="pi_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="sprint_id", referencedColumnName="id")}
     * )
     */
    private $sprints;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Epic")
     * @ORM\JoinTable(
     *      name="program_increment_epic",
     *      joinColumns={@ORM\JoinColumn(name="pi_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="sprint_id", referencedColumnName="id")}
     * )
     */
    private $epics;

    public function __construct()
    {
        $this->projects = new ArrayCollection();
        $this->teams = new ArrayCollection();
        $this->sprints = new ArrayCollection();
        $this->epics = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection|Project[]
     */
    public function getProjects(): Collection
    {
        return $this->projects;
    }

    public function addProject(Project $project): self
    {
        if (!$this->projects->contains($project)) {
            $this->projects[] = $project;
        }

        return $this;
    }

    public function removeProject(Project $project): self
    {
        if ($this->projects->contains($project)) {
            $this->projects->removeElement($project);
        }

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
        }

        return $this;
    }

    public function removeEpic(Epic $epic): self
    {
        if ($this->epics->contains($epic)) {
            $this->epics->removeElement($epic);
        }

        return $this;
    }
}
