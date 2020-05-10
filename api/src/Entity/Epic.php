<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource()
 * @ORM\Entity(repositoryClass="App\Repository\EpicRepository")
 */
class Epic extends AbstractEntity
{
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\EpicStatus")
     * @ORM\JoinColumn(nullable=false)
     */
    private $status;

    public function getStatus(): ?EpicStatus
    {
        return $this->status;
    }

    public function setStatus(?EpicStatus $status): self
    {
        $this->status = $status;

        return $this;
    }
}
