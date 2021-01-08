<?php

namespace App\ServerSentEvent;

use App\Entity\Workitem;
use App\Handler\GettingEstimatesHandler;
use App\Repository\ProgramIncrementRepository;
use App\Repository\ProjectSettingsRepository;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Mercure\PublisherInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Routing\RouterInterface;

class EstimateChangesListener implements EventSubscriberInterface
{
    private const DOCTRINE_SCHEDULED_ENTITY_GETTERS = [
        'getScheduledEntityUpdates',
        'getScheduledEntityDeletions',
    ];

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var PublisherInterface
     */
    private $publisher;

    /**
     * @var GettingEstimatesHandler
     */
    private $gettingEstimatesHandler;

    private ProjectSettingsRepository $projectSettingsRepository;

    private $affectedProjectIds = [];

    public function __construct(
        RouterInterface $router,
        PublisherInterface $publisher,
        GettingEstimatesHandler $gettingEstimatesHandler,
        ProjectSettingsRepository $projectSettingsRepository
    ) {
        $this->router = $router;
        $this->publisher = $publisher;
        $this->gettingEstimatesHandler = $gettingEstimatesHandler;
        $this->projectSettingsRepository = $projectSettingsRepository;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::RESPONSE => 'sendEstimate',
        ];
    }

    public function onFlush(OnFlushEventArgs $eventArgs): void
    {
        $uow = $eventArgs->getEntityManager()->getUnitOfWork();
        foreach (self::DOCTRINE_SCHEDULED_ENTITY_GETTERS as $getterName) {
            foreach ($uow->$getterName() as $entity) {
                if ($entity instanceof Workitem) {
                    $this->affectedProjectIds[] = $entity->getProject()->getId();
                }
            }
        }
    }

    public function sendEstimate(): void
    {
        if (!$this->affectedProjectIds) {
            return;
        }

        $affectedProjectIds = array_unique($this->affectedProjectIds);
        $projectsSettings = $this->projectSettingsRepository->findBy(['project' => $affectedProjectIds]);
        $processedPIs = [];
        foreach ($projectsSettings as $ps) {
            $pi = $ps->getProgramIncrement();
            if (in_array($pi, $processedPIs, true)) {
                continue;
            }

            $processedPIs[] = $pi;
            $data = json_encode(($this->gettingEstimatesHandler)($pi));
            $piEstimateIri = $this->router->generate(
                'api_program_increments_get_estimates_item',
                ['id' => $pi->getId()],
                RouterInterface::ABSOLUTE_URL
            );
            ($this->publisher)(new Update($piEstimateIri, $data));
        }
    }
}
