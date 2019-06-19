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
                'admin' => 'admin/admin/index',
                'admin/<action:(index|login|logout|restore|checkpoint)>' => 'admin/admin/<action>',
                [
                    'pattern' => 'admin/index',
                    'route' => 'admin/admin/index',
                    'suffix' => '',
                ], [
                    'pattern' => 'admin/<action:(index|login|logout|restore|checkpoint)>',
                    'route' => 'admin/admin/<action>',
                    'suffix' => '',
                ],
                '<module:admin>/<controller:\w+>' => '<module>/<controller>',
                '<module:admin>/<submodule:\w+>/<controller:\w+>' => '<module>/<submodule>/<controller>',
                '<module:admin>/<controller:\w+>/<action:\w+>' => '<module>/<controller>/<action>',
                '<module:admin>/<submodule:\w+>/<controller:\w+>/<action:\w+>' => '<module>/<submodule>/<controller>/<action>',
                [
                    'pattern' => '<module:admin>/',
                    'route' => '<module>/admin/index',
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

        // Register language of user interface
        if(!($app instanceof \yii\console\Application)) {
            $lang = $app->session->get('language', false);
            if ($app->request->get('lang', false)) {
                $lang = $app->request->get('lang');
                $app->session->set('language', $lang);
                $app->language = $lang;
            } else if (isset($lang)) {
                $lang = $app->session->get('language');
                $app->language = $lang;
            }
        }

        // Configure administrative panel
        $app->setComponents([
            'dashboard' => [
                'class' => 'wdmg\admin\components\Dashboard'
            ]
        ]);

        // Loading all support modules
        /*$migrationLookup = [];
        $support = $module->getSupportModules();
        $extensions = $module->module->extensions;
        foreach ($support as $name) {
            if ($extensions[$name]) {
                //var_dump($extensions[$name]);
                $alias = array_key_first($extensions[$name]['alias']);
                $basePath = Yii::getAlias($alias).'/Module';
                var_dump($instance = new $basePath());

            }
        }*/

        // Loading all modules
        $migrationLookup = [];
        $support = $module->getSupportModules();
        foreach (Yii::$app->extensions as $extension) {
            if (in_array($extension['name'], $support) && array_key_exists($extension['name'], $module->packages)) {

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