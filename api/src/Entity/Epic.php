<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource(attributes={"order"={"wsjf": "DESC"}})
 * @ApiFilter(SearchFilter::class, properties={"project": "exact"})
 * @ORM\Entity(repositoryClass="App\Repository\EpicRepository")
 */
class Epic extends AbstractEntity
{
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\EpicStatus")
     * @ORM\JoinColumn(nullable=false)
     */
    private $status;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Project")
     * @ORM\JoinColumn(nullable=false)
     */
    private $project;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $wsjf;

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
}
