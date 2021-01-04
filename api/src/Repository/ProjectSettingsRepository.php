<?php

namespace App\Repository;

use App\Entity\ProjectSettings;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ProjectSettings|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProjectSettings|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProjectSettings[]    findAll()
 * @method ProjectSettings[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProjectSettingsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProjectSettings::class);
    }
}
