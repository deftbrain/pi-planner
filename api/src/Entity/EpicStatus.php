<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource(attributes={"order"={"sortOrder"}})
 * @ORM\Entity(repositoryClass="App\Repository\EpicStatusRepository")
 */
class EpicStatus extends AbstractEntity
{
    /**
     * @ORM\Column(type="integer")
     */
    private int $sortOrder;

    public function getSortOrder(): ?int
    {
        return $this->sortOrder;
    }

    public function setSortOrder(int $sortOrder): self
    {
        $this->sortOrder = $sortOrder;

        return $this;
    }
}
