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

    <?php
        $module = Yii::$app->controller->module;

        $info['id'] = $module->id;
        $info['uniqueId'] = $module->getUniqueId();
        $info['name'] = str_replace(Yii::getAlias('@vendor').'/',"", $module->getBasePath());
        $info['label'] = $module->name;
        $info['version'] = $module->version;
        $info['vendor'] = $module->vendor;
        $info['alias'] = $module->getBaseAlias();
        $info['paths']['basePath'] = $module->getBasePath();
        $info['paths']['controllerPath'] = $module->getControllerPath();
        $info['paths']['layoutPath'] = $module->getLayoutPath();
        $info['paths']['viewPath'] = $module->getViewPath();
        $info['components'] = $module->getComponents();

        $info['parent']['id'] = $module->module->id;
        $info['parent']['uniqueId'] = $module->module->getUniqueId();
        $info['parent']['version'] = $module->module->getVersion();
        $info['parent']['paths']['basePath'] = $module->module->getBasePath();
        $info['parent']['paths']['controllerPath'] = $module->module->getControllerPath();
        $info['parent']['paths']['layoutPath'] = $module->module->getLayoutPath();
        $info['parent']['paths']['viewPath'] = $module->module->getViewPath();

        $info['extensions'] = Yii::$app->extensions;

        echo '<pre>'.var_export(count($module->getMetaData()), true).'</pre>';
        echo '<pre>'.var_export(count($info), true).'</pre>';
        echo '<pre>'.var_export(strcmp(json_encode($info), json_encode($module->getMetaData())), true).'</pre>';
        echo '<pre>'.var_export(strspn(json_encode($info) ^ json_encode($module->getMetaData()), "\0"), true).'</pre>';
        //echo '<pre>'.var_export(xdiff_string_diff(json_encode($info), json_encode($module->getMetaData())), true).'</pre>';

        function multi_diff($arr1 ,$arr2) {
            $result = array();
            foreach ($arr1 as $k=>$v){
                if(!isset($arr2[$k])){
                    $result[$k] = $v;
                } else {
                    if(is_array($v) && is_array($arr2[$k])){
                        $diff = multi_diff($v, $arr2[$k]);
                        if(!empty($diff))
                            $result[$k] = $diff;
                    }
                }
            }
            return $result;
        }

        echo '<pre>'.var_export(multi_diff($info, $module->getMetaData()), true).'</pre>';
        echo '<pre>'.var_export(serialize($info) == serialize($module->getMetaData()), true).'</pre>';
        /*
        echo '<pre>'.var_export($info, true).'</pre>';
        echo '<pre>'.var_export($module->getMetaData(), true).'</pre>';
        */

    ?>
</div>

<?php echo $this->render('../_debug'); ?>
