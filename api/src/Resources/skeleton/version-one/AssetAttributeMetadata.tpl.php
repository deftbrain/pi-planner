<?= "<?php\n" ?>

namespace <?= $namespace ?>;

use App\Integration\VersionOne\AssetMetadata\AttributeInterface;

class <?= $class_name ?> implements AttributeInterface
{
    public static function getName(): string
    {
        return '<?= $name ?>';
    }

    public function isMultiValue(): bool
    {
        return <?= $is_multi_value ? 'true' : 'false' ?>;
    }

    public function isRelation(): bool
    {
        return <?= $is_relation ? 'true' : 'false' ?>;
    }

    public function getRelatedAsset(): ?string
    {
        return <?= $is_relation ? "'$related_asset'" : 'null' ?>;
    }
}
