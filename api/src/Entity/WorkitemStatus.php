<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\WorkitemStatusRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource(attributes={"order": {"sortOrder"}})
 * @ORM\Entity(repositoryClass=WorkitemStatusRepository::class)
 */
class WorkitemStatus extends AbstractEntity
{
    /**
     * @ORM\Column(type="integer")
     */
    private $sortOrder;

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
