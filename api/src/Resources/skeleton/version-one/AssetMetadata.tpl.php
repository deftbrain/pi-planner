<?= "<?php\n" ?>

namespace <?= $namespace ?>;

use App\VersionOne\AssetMetadata\Asset;

final class <?= $class_name ?> extends Asset
{
    public function __construct()
    {
        $this->attributes = [
<?php foreach ($attribute_classes as $class): ?>
            <?= "new $class,\n" ?>
<?php endforeach ?>
        ];
    }
}
