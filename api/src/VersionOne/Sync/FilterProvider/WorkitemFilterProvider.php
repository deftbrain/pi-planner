<?php

namespace App\VersionOne\Sync\FilterProvider;

use App\Entity\Epic;
use App\VersionOne\AssetMetadata\Workitem;
use Doctrine\ORM\EntityManagerInterface;

class WorkitemFilterProvider implements FilterProviderInterface
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
        $epics = $this->entityManager->createQueryBuilder()
            ->from(Epic::class, 'e')
            ->distinct()
            ->select('e.externalId')
            ->getQuery()
            ->getScalarResult();

        if (!$epics) {
            return [];
        }

        return [Workitem::ATTRIBUTE_SUPER => array_column($epics, 'externalId')];
    }
}
