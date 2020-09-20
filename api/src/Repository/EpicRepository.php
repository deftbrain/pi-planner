<?php

namespace App\Repository;

use App\Entity\Epic;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Epic|null find($id, $lockMode = null, $lockVersion = null)
 * @method Epic|null findOneBy(array $criteria, array $orderBy = null)
 * @method Epic[]    findAll()
 * @method Epic[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EpicRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Epic::class);
    }

    // /**
    //  * @return Epic[] Returns an array of Epic objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Epic
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
