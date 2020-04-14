<?php

namespace App\Repository;

use App\Entity\ProgramIncrement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method ProgramIncrement|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProgramIncrement|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProgramIncrement[]    findAll()
 * @method ProgramIncrement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProgramIncrementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProgramIncrement::class);
    }
}
