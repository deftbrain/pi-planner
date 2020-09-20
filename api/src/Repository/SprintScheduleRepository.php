<?php

namespace App\Repository;

use App\Entity\SprintSchedule;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SprintSchedule|null find($id, $lockMode = null, $lockVersion = null)
 * @method SprintSchedule|null findOneBy(array $criteria, array $orderBy = null)
 * @method SprintSchedule[]    findAll()
 * @method SprintSchedule[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SprintScheduleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SprintSchedule::class);
    }

    // /**
    //  * @return SprintSchedule[] Returns an array of SprintSchedule objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?SprintSchedule
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
