<?php

namespace App\VersionOne\Sync\FilterProvider;

use App\Entity\EpicStatus;
use App\Entity\ProgramIncrement;
use App\Entity\Project;
use App\VersionOne\AssetMetadata\Epic\ScopeAttribute;
use App\VersionOne\AssetMetadata\Epic\StatusAttribute;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Routing\RouterInterface;

class EpicFilterProvider implements FilterProviderInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var ParameterBagInterface
     */
    private $params;

    /**
     * @var RouterInterface
     */
    private $router;

    public function __construct(
        EntityManagerInterface $entityManager,
        ParameterBagInterface $params,
        RouterInterface $router
    ) {
        $this->entityManager = $entityManager;
        $this->params = $params;
        $this->router = $router;
    }

    public function getFilter(): array
    {
        $projectIds = array_column(
            $this->entityManager->createQueryBuilder()
                ->from(ProgramIncrement::class, 'pi')
                ->distinct()
                ->select('IDENTITY(pi.project) AS project')
                ->getQuery()
                ->getScalarResult(),
            'project'
        );
        if (!$projectIds) {
            return [];
        }

        $projects = $this->entityManager->createQueryBuilder()
            ->from(Project::class, 'p')
            ->select('p.externalId')
            ->andWhere('p.id IN (:ids)')
            ->setParameter('ids', $projectIds)
            ->getQuery()
            ->getScalarResult();

        /** @var EpicStatus $epicStatus */
        $epicStatus = $this->entityManager->getRepository(EpicStatus::class)
            ->findOneBy(['name' => $this->params->get('version_one.filter.epic.status_name'),]);

        return [
            ScopeAttribute::getName() => array_column($projects, 'externalId'),
            StatusAttribute::getName() => $epicStatus->getExternalId(),
        ];
    }
}
