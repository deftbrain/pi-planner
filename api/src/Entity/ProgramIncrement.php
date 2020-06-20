<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\GetProgramIncrementEstimates;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

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
 *     }
 * )
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
     * @ORM\Column(type="json")
     */
    private $projectSettings = [];

    public function __construct()
    {
        $this->projects = new ArrayCollection();
        $this->teams = new ArrayCollection();
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

    public function getProjectSettings(): array
    {
        return $this->projectSettings;
    }

    public function setProjectSettings(array $projectSettings): self
    {
        $this->projectSettings = $projectSettings;

        return $this;
    }
}
