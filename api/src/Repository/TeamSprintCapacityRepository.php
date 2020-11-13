<?php

namespace App\Repository;

use App\Entity\TeamSprintCapacity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TeamSprintCapacity|null find($id, $lockMode = null, $lockVersion = null)
 * @method TeamSprintCapacity|null findOneBy(array $criteria, array $orderBy = null)
 * @method TeamSprintCapacity[]    findAll()
 * @method TeamSprintCapacity[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TeamSprintCapacityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TeamSprintCapacity::class);
    }
}
