<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\GetProgramIncrementEstimates;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @todo Figure out how to make Swagger show entity different from ProgramIncrement as returned type for the get_estimates operation
 * @ApiResource(
 *     collectionOperations={"get", "post"},
 *     itemOperations={
 *         "get",
 *         "put",
 *         "delete",
 *         "get_estimates": {
 *             "method": "GET",
 *             "path": "/program_increments/{id}/estimates",
 *             "controller": GetProgramIncrementEstimates::class
 *         }
 *     },
 *     mercure={"private": true},
 *     normalizationContext={"groups"={"programIncrement"}}
 * )
 * @ORM\Entity(repositoryClass="App\Repository\ProgramIncrementRepository")
 */
class ProgramIncrement
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"programIncrement"})
     */
    private $id;

    /**
     * @ApiProperty(iri="http://schema.org/name")
     * @ORM\Column(type="string", length=255)
     * @Groups({"programIncrement"})
     */
    private $name;

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
     * @ORM\OneToMany(targetEntity=TeamSprintCapacity::class, mappedBy="programIncrement", orphanRemoval=true)
     * @Groups({"programIncrement"})
     */
    private $teamSprintCapacities;

    public function __construct()
    {
        $this->sprints = new ArrayCollection();
        $this->teamSprintCapacities = new ArrayCollection();
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
            $teamSprintCapacity->setProgramIncrement($this);
        }

        return $this;
    }

    public function removeTeamSprintCapacity(TeamSprintCapacity $teamSprintCapacity): self
    {
        if ($this->teamSprintCapacities->contains($teamSprintCapacity)) {
            $this->teamSprintCapacities->removeElement($teamSprintCapacity);
            // set the owning side to null (unless already changed)
            if ($teamSprintCapacity->getProgramIncrement() === $this) {
                $teamSprintCapacity->setProgramIncrement(null);
            }
        }

        return $this;
    }
}
