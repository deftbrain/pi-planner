<?php

namespace App\Repository;

use App\Entity\Workitem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Workitem|null find($id, $lockMode = null, $lockVersion = null)
 * @method Workitem|null findOneBy(array $criteria, array $orderBy = null)
 * @method Workitem[]    findAll()
 * @method Workitem[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WorkitemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Workitem::class);
    }

    // /**
    //  * @return Workitem[] Returns an array of Workitem objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('w.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Workitem
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
