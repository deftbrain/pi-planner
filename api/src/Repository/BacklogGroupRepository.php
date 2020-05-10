<?php

namespace App\Repository;

use App\Entity\BacklogGroup;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method BacklogGroup|null find($id, $lockMode = null, $lockVersion = null)
 * @method BacklogGroup|null findOneBy(array $criteria, array $orderBy = null)
 * @method BacklogGroup[]    findAll()
 * @method BacklogGroup[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BacklogGroupRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BacklogGroup::class);
    }

    // /**
    //  * @return BacklogGroup[] Returns an array of BacklogGroup objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?BacklogGroup
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
