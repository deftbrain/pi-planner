<?php

namespace App\Handler;

use App\Entity\ProgramIncrement;
use App\Entity\Workitem;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\RouterInterface;

final class GettingEstimatesHandler
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(RouterInterface $router, EntityManagerInterface $entityManager)
    {
        $this->router = $router;
        $this->entityManager = $entityManager;
    }

    public function __invoke(ProgramIncrement $programIncrement): array
    {
        $projectIris = array_column($programIncrement->getProjectSettings(), 'project');
        if (!$projectIris) {
            return [];
        }

        $projectIds = [];
        foreach ($projectIris as $projectIri) {
            $parameters = $this->router->match($projectIri);
            $projectIds[] = $parameters['id'];
        }

        $qb = $this->entityManager->createQueryBuilder();
        $result = $qb
            ->from(Workitem::class, 'w')
            ->select(
                'IDENTITY(w.epic) AS epic',
                'IDENTITY(w.team) AS team',
                'IDENTITY(w.sprint) AS sprint',
                'SUM(w.estimateFrontend) AS frontend',
                'SUM(w.estimateBackend) AS backend'
            )
            ->andWhere('w.isDeleted = false')
            ->andWhere($qb->expr()->in('w.project', $projectIds))
            ->andWhere($qb->expr()->orX('w.estimateFrontend > 0', 'w.estimateBackend > 0'))
            ->groupBy('w.epic', 'w.team', 'w.sprint')
            ->getQuery()
            ->getScalarResult();

        foreach ($result as &$estimate) {
            $estimate['epic'] = $this->router->generate('api_epics_get_item', ['id' => $estimate['epic']]);
            $estimate['team'] = isset($estimate['team'])
                ? $this->router->generate('api_teams_get_item', ['id' => $estimate['team']]) : null;
            $estimate['sprint'] = isset($estimate['sprint'])
                ? $this->router->generate('api_sprints_get_item', ['id' => $estimate['sprint']])
                : null;
            $estimate['frontend'] = (float) $estimate['frontend'];
            $estimate['backend'] = (float) $estimate['backend'];
        }

        return $result;
    }
}
