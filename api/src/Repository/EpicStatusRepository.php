<?php

namespace App\Repository;

use App\Entity\EpicStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method EpicStatus|null find($id, $lockMode = null, $lockVersion = null)
 * @method EpicStatus|null findOneBy(array $criteria, array $orderBy = null)
 * @method EpicStatus[]    findAll()
 * @method EpicStatus[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EpicStatusRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EpicStatus::class);
    }

    // /**
    //  * @return EpicStatus[] Returns an array of EpicStatus objects
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
    public function findOneBySomeField($value): ?EpicStatus
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
