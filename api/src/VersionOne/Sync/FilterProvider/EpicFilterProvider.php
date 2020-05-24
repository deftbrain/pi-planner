<?php


namespace App\VersionOne\Sync\FilterProvider;

use App\Entity\EpicStatus;
use App\Entity\ProgramIncrement;
use App\VersionOne\AssetMetadata\Epic;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class EpicFilterProvider
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var ParameterBagInterface
     */
    private $params;

    public function __construct(EntityManagerInterface $entityManager, ParameterBagInterface $params)
    {
        $this->entityManager = $entityManager;
        $this->params = $params;
    }

    public function getFilter(): array
    {
        $projects = $this->entityManager->createQueryBuilder()
            ->from(ProgramIncrement::class, 'pi')
            ->distinct()
            ->select('p.externalId')
            ->join('pi.projects', 'p')
            ->getQuery()
            ->getScalarResult();

        /** @var EpicStatus $epicStatus */
        $epicStatus = $this->entityManager->getRepository(EpicStatus::class)
            ->findOneBy(['name' => $this->params->get('version_one.filter.epic.status_name')]);

        return [
            Epic::ATTRIBUTE_SCOPE => array_column($projects, 'externalId'),
            Epic::ATTRIBUTE_STATUS => $epicStatus->getExternalId(),
        ];
    }
}
