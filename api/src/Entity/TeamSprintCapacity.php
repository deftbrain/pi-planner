<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Repository\TeamSprintCapacityRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ApiResource(attributes={"order"={"team.name"}})
 * @ApiFilter(SearchFilter::class, properties={"projectSettings": "exact"})
 * @ORM\Entity(repositoryClass=TeamSprintCapacityRepository::class)
 */
class TeamSprintCapacity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Team::class)
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"programIncrement"})
     */
    private $team;

    /**
     * @ORM\ManyToOne(targetEntity=Sprint::class)
     * @Groups({"programIncrement"})
     */
    private $sprint;

    /**
     * @ORM\Column(type="json")
     * @Groups({"programIncrement"})
     */
    private $capacity = [];

    /**
     * @ORM\ManyToOne(targetEntity=ProjectSettings::class, inversedBy="teamSprintCapacities")
     * @ORM\JoinColumn(nullable=false)
     */
    private $projectSettings;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getCapacity(): ?array
    {
        return $this->capacity;
    }

    public function setCapacity(array $capacity): self
    {
        $this->capacity = $capacity;

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
}
