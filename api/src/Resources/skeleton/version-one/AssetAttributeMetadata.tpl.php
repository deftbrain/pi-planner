<?= "<?php\n" ?>

namespace <?= $namespace ?>;

use App\VersionOne\AssetMetadata\AttributeInterface;

final class <?= $class_name ?> implements AttributeInterface
{
    public function getName(): string
    {
        return '<?= $name ?>';
    }

    public function isReadOnly(): bool
    {
        return <?= $is_read_only ? 'true' : 'false' ?>;
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
