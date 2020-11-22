<?= "<?php\n" ?>

namespace <?= $namespace ?>;

use App\VersionOne\AssetMetadata\<?= $is_base_type ? 'AbstractAssetMetadata' : 'BaseAsset\\BaseAssetAssetMetadata' ?>;

class <?= $class_name ?> extends <?= $is_base_type ? 'AbstractAssetMetadata' : 'BaseAssetAssetMetadata' ?>

{
<?php if ($attribute_classes): ?>
    public function __construct()
    {
<?php if (!$is_base_type): ?>
        parent::__construct();
<?php endif ?>
        $this->attributes = array_merge(
            $this->attributes,
            [
<?php foreach ($attribute_classes as $class): ?>
                <?= "new $class,\n" ?>
<?php endforeach ?>
            ]
        );
    }

<?php endif ?>
    public function getType(): string
    {
        return '<?= $asset_type ?>';
    }
}
