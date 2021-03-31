<?php

namespace App\Integration\Serializer;

/**
 * Keep this class abstract (to make it ignorable by a serializer) otherwise the wrong response returned when
 * requesting the '/' or '/docs' endpoints because custom normalizers have higher priority than other normalizers.
 */
abstract class ObjectNormalizer extends \Symfony\Component\Serializer\Normalizer\ObjectNormalizer
{
    public const FORCE_UPDATE = 'force_update';
    public const PARENT_OBJECT_CLASS = 'parent_object_class';
    public const ISSUE_COMPLETED = 'issue_completed';
}
