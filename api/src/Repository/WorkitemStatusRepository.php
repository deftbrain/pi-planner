<?php

namespace App\Repository;

use App\Entity\WorkitemStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method WorkitemStatus|null find($id, $lockMode = null, $lockVersion = null)
 * @method WorkitemStatus|null findOneBy(array $criteria, array $orderBy = null)
 * @method WorkitemStatus[]    findAll()
 * @method WorkitemStatus[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WorkitemStatusRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WorkitemStatus::class);
    }
}
