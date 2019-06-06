<?php

namespace wdmg\admin;

/**
 * @author          Alexsander Vyshnyvetskyy <alex.vyshnyvetskyy@gmail.com>
 * @copyright       Copyright (c) 2019 W.D.M.Group, Ukraine
 * @license         https://opensource.org/licenses/MIT Massachusetts Institute of Technology (MIT) License
 */

use yii\base\BootstrapInterface;
use Yii;
use yii\helpers\ArrayHelper;


class Bootstrap implements BootstrapInterface
{


    public function bootstrap($app)
    {
        // Get the module instance
        $module = Yii::$app->getModule('admin');

        // Get URL path prefix if exist
        /*if (isset($module->routePrefix)) {
            $app->getUrlManager()->enableStrictParsing = true;
            $prefix = $module->routePrefix . '/';
        } else {
            $prefix = '';
        }*/

        // Add module URL rules
        $app->getUrlManager()->enablePrettyUrl = true;
        $app->getUrlManager()->showScriptName = false;
        $app->getUrlManager()->enableStrictParsing = true;

        $app->getUrlManager()->addRules(
            [
                '/admin' => 'admin/admin/index',
                '/admin/login' => 'admin/admin/login',
                [
                    'pattern' => '/admin',
                    'route' => 'admin/admin/index',
                    'suffix' => '',
                ], [
                    'pattern' => '/admin/login',
                    'route' => 'admin/admin/login',
                    'suffix' => '',
                ],

                '<action>'=>'site/<action>',
                '<controller:\w+>/<id:\d+>' => '<controller>/view',
                '<controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',
                '<controller:\w+>/<action:\w+>' => '<controller>/<action>',
                '<module:\w+>/<controller:\w+>/<action:\w+>/<id:\d+>' => '<module>/<controller>/<action>',
                '<module:\w+>/<controller:\w+>/<action:\w+>' => '<module>/<controller>/<action>',
                '<module:\w+>/<controller:\w+>' => '<module>/<controller>/index',

                '<module:admin>/<controller:\w+>' => '<module>/<controller>',
                '<module:admin>/<submodule:\w+>/<controller:\w+>' => '<module>/<submodule>/<controller>',
                '<module:admin>/<controller:\w+>/<action:\w+>' => '<module>/<controller>/<action>',
                '<module:admin>/<submodule:\w+>/<controller:\w+>/<action:\w+>' => '<module>/<submodule>/<controller>/<action>',
                [
                    'pattern' => '<module:admin>/',
                    'route' => '<module>/dashboard/index',
                    'suffix' => '',
                ], [
                    'pattern' => '<module:admin>/<controller:\w+>/',
                    'route' => '<module>/<controller>',
                    'suffix' => '',
                ], [
                    'pattern' => '<module:admin>/<controller:\w+>/<action:\w+>',
                    'route' => '<module>/<controller>/<action>',
                    'suffix' => '',
                ],
            ],
            true
        );

        // Configure administrative panel
        $app->setComponents([
            'dashboard' => [
                'class' => 'wdmg\admin\components\Dashboard'
            ]
        ]);

        // Loading all modules
        $migrationLookup = [];
        $extensions = $module->module->extensions;
        foreach ($extensions as $extension) {
            if (array_key_exists($extension['name'], $module->packages)) {

                $package = (object)$module->packages[$extension['name']];
                $module->setModule($package->moduleId, ArrayHelper::merge([
                    //Yii::$app->setModule($package->moduleId, ArrayHelper::merge([
                    'class' => $package->moduleClass
                ], $package->moduleOptions));


                $installed = Yii::$app->getModule('admin/'.$package->moduleId);
                if ($installed) {

                    // Configure dashboard
                    $installed->layout = 'dashboard';
                    $installed->layoutPath = '@wdmg/admin/views/layouts';

                    $migrationLookup[] = $installed->getBaseAlias() . '/migrations';

                    if (isset($module->routePrefix))
                        $installed->routePrefix = $module->routePrefix;
                    if ($installed instanceof yii\base\BootstrapInterface) {
                        $installed->bootstrap(Yii::$app);
                        //Yii::$app->bootstrap[] = $package->moduleClass;
                    } else {
                        Yii::$app->bootstrap[] = $package->bootstrapClass;
                    }
                }
            }
        }

        // Configure migrations for all modules
        if (Yii::$app instanceof \yii\console\Application) {
            Yii::$app->controllerMap['migrate'] = [
                'class' => 'wdmg\admin\commands\MigrateController',
                'migrationLookup' => $migrationLookup
            ];
        }
    }
}