<?php

namespace wdmg\admin;
use yii\web\AssetBundle;

class AdminAsset extends AssetBundle
{
    public $sourcePath = '@vendor/wdmg/yii2-admin/assets';
    /*public $basePath = '@webroot';
    public $baseUrl = '@web';*/

    public $js = [
        'js/sticky-sidebar.js',
        'js/admin.js'
    ];

    public $css = [

    ];

    public $depends = [
        'yii\web\YiiAsset',
    ];

    public function init()
    {
        parent::init();

        /*\Yii::$app->assetManager->bundles['yii\web\JqueryAsset'] = [
            'sourcePath' => $this->sourcePath,
            'js' => [
                YII_ENV_DEV ? 'js/jquery.js' : 'js/jquery.min.js',
                YII_ENV_DEV ? 'js/helper.js' : 'js/helper.min.js'
            ]
        ];*/
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

        // Common scripts
        $this->js = YII_DEBUG ? ['js/sticky-sidebar.js'] : ['js/sticky-sidebar.min.js'];
        $this->js = YII_DEBUG ? ['js/admin.js'] : ['js/admin.min.js'];

    }

}

?>