<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;

/**
 * @todo Replace capacity props with a value object when https://github.com/api-platform/api-platform/issues/1197 is done.
 * @ApiResource()
 * @ORM\Entity(repositoryClass="App\Repository\TeamSprintCapacityRepository")
 */
class TeamSprintCapacity
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Team")
     * @ORM\JoinColumn(nullable=false)
     */
    private $team;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Sprint")
     * @ORM\JoinColumn(nullable=false)
     */
    private $sprint;

    /**
     * @ORM\Column(type="float")
     * @var float
     */
    private $capacityFrontend;

    /**
     * @ORM\Column(type="float")
     * @var float
     */
    private $capacityBackend;

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

    public function getCapacityFrontend()
    {
        return $this->capacityFrontend;
    }

    public function setCapacityFrontend($capacity): self
    {
        $this->capacityFrontend = $capacity;

        return $this;
    }

    public function getCapacityBackend()
    {
        return $this->capacityBackend;
    }

    public function setCapacityBackend($capacity): self
    {
        $this->capacityBackend = $capacity;

        return $this;
    }
}
