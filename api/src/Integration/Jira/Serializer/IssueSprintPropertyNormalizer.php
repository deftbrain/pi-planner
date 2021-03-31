<?php

namespace App\Integration\Jira\Serializer;

use App\Entity\Sprint;
use App\Entity\Workitem;
use Symfony\Component\Serializer\Normalizer\ContextAwareDenormalizerInterface;

class IssueSprintPropertyNormalizer implements ContextAwareDenormalizerInterface
{
    private const FIELD_END_DATE = 'endDate';
    private const FIELD_STATE = 'state';
    private const NON_CLOSED_SPRINT_STATES = ['active', 'future'];

    private ObjectNormalizer $objectNormalizer;

    public function __construct(ObjectNormalizer $objectNormalizer)
    {
        $this->objectNormalizer = $objectNormalizer;
    }

    public function supportsDenormalization($data, string $type, string $format = null, array $context = []): bool
    {
        return $type === Sprint::class
            && isset($context[ObjectNormalizer::PARENT_OBJECT_CLASS])
            && $context[ObjectNormalizer::PARENT_OBJECT_CLASS] === Workitem::class
            && $this->objectNormalizer->supportsDenormalization($data, $type, $format, $context);
    }

    public function denormalize($data, string $type, string $format = null, array $context = [])
    {
        if ($data === null) {
            // The $data will be equal to null when an issue is not linked to any active/future sprint and it
            // never was in a sprint which has been closed, in any other cases the $data will be a non-empty array
            return null;
        }

        $sprints = $data;
        $nonClosedSprints = array_filter(
            $sprints,
            // A future sprint might not have the endDate field (https://jira.atlassian.com/browse/JSWCLOUD-7740)
            // so use the state field to find a non-closed sprint
            static fn ($sprint) => in_array($sprint[self::FIELD_STATE], self::NON_CLOSED_SPRINT_STATES, true)
        );

        if ($nonClosedSprints) {
            // Only one non-closed sprint can be linked to an issue so just take the first element from the array
            $latestSprint = reset($nonClosedSprints);
        } elseif (!empty($context[ObjectNormalizer::ISSUE_COMPLETED])) {
            usort(
                $sprints,
                static fn ($a, $b) => strtotime($a[self::FIELD_END_DATE]) <=> strtotime($b[self::FIELD_END_DATE])
            );
            $latestSprint = end($sprints);
        }

        return isset($latestSprint)
            ? $this->objectNormalizer->denormalize($latestSprint, $type, $format, $context)
            : null;
    }
}
