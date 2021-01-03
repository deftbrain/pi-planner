<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource(attributes={"order"={"schedule", "startDate"}})
 * @ApiFilter(SearchFilter::class, properties={"id": "exact", "schedule": "exact"})
 * @ORM\Entity(repositoryClass="App\Repository\SprintRepository")
 */
class Sprint extends AbstractEntity
{
    /**
     * @ORM\Column(type="date")
     */
    private $startDate;

    /**
     * @ORM\Column(type="date")
     */
    private $endDate;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\SprintSchedule", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $schedule;

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTimeInterface $endDate): self
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getSchedule(): ?SprintSchedule
    {
        return $this->schedule;
    }

    public function setSchedule(?SprintSchedule $schedule): self
    {
        $this->schedule = $schedule;

        return $this;
    }
}
