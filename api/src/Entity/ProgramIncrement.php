<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\GetProgramIncrementEstimates;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

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
     * @ORM\OneToMany(targetEntity=ProjectSettings::class, mappedBy="programIncrement", orphanRemoval=true)
     * @Groups({"programIncrement"})
     */
    private $projectsSettings;

    public function __construct()
    {
        $this->projectsSettings = new ArrayCollection();
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
     * @return Collection|ProjectSettings[]
     */
    public function getProjectsSettings(): Collection
    {
        return $this->projectsSettings;
    }

    public function addProjectSetting(ProjectSettings $projectSetting): self
    {
        if (!$this->projectsSettings->contains($projectSetting)) {
            $this->projectsSettings[] = $projectSetting;
            $projectSetting->setProgramIncrement($this);
        }

        return $this;
    }

    public function removeProjectSetting(ProjectSettings $projectSetting): self
    {
        if ($this->projectsSettings->contains($projectSetting)) {
            $this->projectsSettings->removeElement($projectSetting);
            // set the owning side to null (unless already changed)
            if ($projectSetting->getProgramIncrement() === $this) {
                $projectSetting->setProgramIncrement(null);
            }
        }

        return $this;
    }
}
