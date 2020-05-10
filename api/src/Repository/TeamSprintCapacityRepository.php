<?php

namespace App\Repository;

use App\Entity\TeamSprintCapacity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

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

    // /**
    //  * @return TeamSprintCapacity[] Returns an array of TeamSprintCapacity objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?TeamSprintCapacity
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
