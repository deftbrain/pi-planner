<?php


namespace App\VersionOne\Sync\FilterProvider;


use App\Entity\Epic;
use App\VersionOne\AssetMetadata\Workitem;
use Doctrine\ORM\EntityManagerInterface;

class WorkitemFilterProvider
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getFilter(): array
    {
        $v1EpicIds = $this->entityManager->createQueryBuilder()
            ->from(Epic::class, 'e')
            ->distinct()
            ->select('e.externalId')
            ->getQuery()
            ->getScalarResult();

        return [Workitem::ATTRIBUTE_SUPER => array_column($v1EpicIds, 'externalId')];
    }
}
