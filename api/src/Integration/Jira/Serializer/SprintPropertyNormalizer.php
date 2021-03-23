<?php

namespace App\Integration\Jira\Serializer;

use App\Entity\Sprint;

class SprintPropertyNormalizer extends ObjectNormalizer
{
    public function supportsNormalization($data, string $format = null): bool
    {
        return false;
    }

    public function supportsDenormalization($data, string $type, string $format = null, array $context = []): bool
    {
        return $type === Sprint::class && $this->isNotEmptySprintPropertyValue($data);
    }

    private function isNotEmptySprintPropertyValue($data): bool
    {
        if (!is_array($data)) {
            return false;
        }

        $sprints = array_filter(
            $data,
            fn ($value, $key) => is_int($key) && $this->isSprintData($value),
            ARRAY_FILTER_USE_BOTH
        );
        $areAllElementsSprint = count($data) === count($sprints);
        return $areAllElementsSprint;
    }

    private function isSprintData($value): bool
    {
        return is_array($value) && isset($value['id'], $value['name'], $value['state'], $value['boardId']);
    }

    public function denormalize($data, string $type, string $format = null, array $context = [])
    {
        foreach ($data as $sprint) {
            if (in_array($sprint['state'], ['active', 'future'], true)) {
                return parent::denormalize($sprint, $type, $format, $context);
            }
        }

        // There is no an active/future sprint in the list so the sprint property should bet set to null
        return null;
    }


}
