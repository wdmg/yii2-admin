<?php

use yii\helpers\Html;

/* @var $this yii\web\View */

$this->title = $this->context->module->name;
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="page-header">
    <h1>
        <?= Html::encode($this->title) ?> <small class="text-muted pull-right">[v.<?= $this->context->module->version ?>]</small>
    </h1>
</div>
<div class="admin-index">
    123
    <?php

    /*$collection = [
        'phpVersion' => PHP_VERSION,
        'yiiVersion' => Yii::getVersion(),
        'application' => [
            'yii' => Yii::getVersion(),
            'name' => Yii::$app->name,
            'version' => Yii::$app->version,
            'language' => Yii::$app->language,
            'sourceLanguage' => Yii::$app->sourceLanguage,
            'charset' => Yii::$app->charset,
            'env' => YII_ENV,
            'debug' => YII_DEBUG,
        ],
        'php' => [
            'version' => PHP_VERSION,
            'xdebug' => extension_loaded('xdebug'),
            'apc' => extension_loaded('apc'),
            'memcache' => extension_loaded('memcache'),
            'memcached' => extension_loaded('memcached'),
        ],
        'extensions' => Yii::$app->extensions,
    ];
    $data = [];
    foreach ($collection['extensions'] as $extension) {
        $data[$extension['name']] = $extension['version'];
    }
    ksort($data);
    var_dump($data);*/
    /*var_dump(Yii::$app->extensions);*/



echo "**** Base modules *****";
    if (Yii::$app->controller->module->getUniqueId() == "admin" && count(Yii::$app->controller->modules) > 0) {
        $modules = Yii::$app->controller->modules;
        foreach ($modules as $module) {
            var_dump($module->id);
            var_dump($module->name);
        }
    }

echo "**** Child`s modules *****";

    if (Yii::$app->controller->module->getUniqueId() == "admin" && count(Yii::$app->controller->module->getModules()) > 0) {
        $modules = Yii::$app->controller->module->getModules();
        foreach ($modules as $module) {
            /*var_dump($module->id);
            var_dump($module->name);*/
            var_dump($module->version);
            var_dump($module->priority);
        }
    }
    ?>

</div>

<?php echo $this->render('../_debug'); ?>
