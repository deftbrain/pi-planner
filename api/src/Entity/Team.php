<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource(attributes={"order"={"name"}})
 * @ORM\Entity(repositoryClass="App\Repository\TeamRepository")
 */
class Team extends AbstractEntity
{
}
