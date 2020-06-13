<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource(attributes={"order"={"name"}})
 * @ApiFilter(SearchFilter::class, properties={"id": "exact"})
 * @ORM\Entity(repositoryClass="App\Repository\TeamRepository")
 */
class Team extends AbstractEntity
{
}
