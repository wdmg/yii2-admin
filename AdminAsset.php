<?php

namespace wdmg\admin;
use yii\web\AssetBundle;

class AdminAsset extends AssetBundle
{
    public $sourcePath = '@vendor/wdmg/yii2-admin/assets';

    public $js = [
        'js/sticky-sidebar.js',
        'js/admin.js'
    ];

    public $depends = [
        'yii\web\YiiAsset',
        'yii\web\JqueryAsset',
        'yii\bootstrap\BootstrapPluginAsset',
        'yii\bootstrap\BootstrapAsset',
    ];

    public function init()
    {
        parent::init();

        \Yii::$app->assetManager->bundles['yii\web\JqueryAsset'] = [
            'sourcePath' => $this->sourcePath,
            'js' => [
                YII_ENV_DEV ? 'js/jquery.js' : 'js/jquery.min.js',
                YII_ENV_DEV ? 'js/helper.js' : 'js/helper.min.js'
            ]
        ];

        \Yii::$app->assetManager->bundles['yii\bootstrap\BootstrapPluginAsset'] = [
            'sourcePath' => $this->sourcePath,
            'js' => [
                YII_ENV_DEV ? 'js/bootstrap.js' : 'js/bootstrap.min.js'
            ]
        ];

        \Yii::$app->assetManager->bundles['yii\bootstrap\BootstrapAsset'] = [
            'sourcePath' => $this->sourcePath,
            'css' => [
                YII_ENV_DEV ? 'css/admin.css' : 'css/admin.min.css'
            ]
        ];

    }
}

?>