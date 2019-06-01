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

    public $packages = [
        "wdmg/yii2-activity" => [
            'moduleId' => 'activity',
            'moduleClass' => 'wdmg\activity\Module',
            'bootstrapClass' => 'wdmg\activity\Bootstrap',
            'moduleOptions' => [
                'routePrefix' => 'admin'
            ],
        ],
        "wdmg/yii2-api" => [
            'moduleId' => 'api',
            'moduleClass' => 'wdmg\api\Module',
            'bootstrapClass' => 'wdmg\api\Bootstrap',
            'moduleOptions' => [
                'routePrefix' => 'admin'
            ],
        ],
        "wdmg/yii2-bookmarks" => [
            'moduleId' => 'bookmarks',
            'moduleClass' => 'wdmg\bookmarks\Module',
            'bootstrapClass' => 'wdmg\bookmarks\Bootstrap',
            'moduleOptions' => [
                'routePrefix' => 'admin'
            ],
        ],
        "wdmg/yii2-comments" => [
            'moduleId' => 'comments',
            'moduleClass' => 'wdmg\comments\Module',
            'bootstrapClass' => 'wdmg\comments\Bootstrap',
            'moduleOptions' => [
                'routePrefix' => 'admin'
            ],
        ],
        "wdmg/yii2-forms" => [
            'moduleId' => 'forms',
            'moduleClass' => 'wdmg\forms\Module',
            'bootstrapClass' => 'wdmg\forms\Bootstrap',
            'moduleOptions' => [
                'routePrefix' => 'admin'
            ],
        ],
        "wdmg/yii2-geo" => [
            'moduleId' => 'geo',
            'moduleClass' => 'wdmg\geo\Module',
            'bootstrapClass' => 'wdmg\geo\Bootstrap',
            'moduleOptions' => [
                'routePrefix' => 'admin'
            ],
        ],
        "wdmg/yii2-likes" => [
            'moduleId' => 'likes',
            'moduleClass' => 'wdmg\likes\Module',
            'bootstrapClass' => 'wdmg\likes\Bootstrap',
            'moduleOptions' => [
                'routePrefix' => 'admin'
            ],
        ],
        "wdmg/yii2-messages" => [
            'moduleId' => 'messages',
            'moduleClass' => 'wdmg\messages\Module',
            'bootstrapClass' => 'wdmg\messages\Bootstrap',
            'moduleOptions' => [
                'routePrefix' => 'admin'
            ],
        ],
        "wdmg/yii2-options" => [
            'moduleId' => 'options',
            'moduleClass' => 'wdmg\options\Module',
            'bootstrapClass' => 'wdmg\options\Bootstrap',
            'moduleOptions' => [
                'routePrefix' => 'admin'
            ],
        ],
        "wdmg/yii2-rbac" => [
            'moduleId' => 'rbac',
            'moduleClass' => 'wdmg\rbac\Module',
            'bootstrapClass' => 'wdmg\rbac\Bootstrap',
            'moduleOptions' => [
                'routePrefix' => 'admin'
            ],
        ],
        "wdmg/yii2-reposts" => [
            'moduleId' => 'reposts',
            'moduleClass' => 'wdmg\reposts\Module',
            'bootstrapClass' => 'wdmg\reposts\Bootstrap',
            'moduleOptions' => [
                'routePrefix' => 'admin'
            ],
        ],
        "wdmg/yii2-reviews" => [
            'moduleId' => 'reviews',
            'moduleClass' => 'wdmg\reviews\Module',
            'bootstrapClass' => 'wdmg\reviews\Bootstrap',
            'moduleOptions' => [
                'routePrefix' => 'admin'
            ],
        ],
        "wdmg/yii2-services" => [
            'moduleId' => 'services',
            'moduleClass' => 'wdmg\services\Module',
            'bootstrapClass' => 'wdmg\services\Bootstrap',
            'moduleOptions' => [
                'routePrefix' => 'admin'
            ],
        ],
        "wdmg/yii2-stats" => [
            'moduleId' => 'stats',
            'moduleClass' => 'wdmg\stats\Module',
            'bootstrapClass' => 'wdmg\stats\Bootstrap',
            'moduleOptions' => [
                'routePrefix' => 'admin'
            ],
        ],
        "wdmg/yii2-tasks" => [
            'moduleId' => 'tasks',
            'moduleClass' => 'wdmg\tasks\Module',
            'bootstrapClass' => 'wdmg\tasks\Bootstrap',
            'moduleOptions' => [
                'routePrefix' => 'admin'
            ],
        ],
        "wdmg/yii2-tickets" => [
            'moduleId' => 'tickets',
            'moduleClass' => 'wdmg\tickets\Module',
            'bootstrapClass' => 'wdmg\tickets\Bootstrap',
            'moduleOptions' => [
                'routePrefix' => 'admin'
            ],
        ],
        "wdmg/yii2-users" => [
            'moduleId' => 'users',
            'moduleClass' => 'wdmg\users\Module',
            'bootstrapClass' => 'wdmg\users\Bootstrap',
            'moduleOptions' => [
                'routePrefix' => 'admin'
            ],
        ],
        "wdmg/yii2-views" => [
            'moduleId' => 'views',
            'moduleClass' => 'wdmg\views\Module',
            'bootstrapClass' => 'wdmg\views\Bootstrap',
            'moduleOptions' => [
                'routePrefix' => 'admin'
            ],
        ],
        "wdmg/yii2-votes" => [
            'moduleId' => 'votes',
            'moduleClass' => 'wdmg\votes\Module',
            'bootstrapClass' => 'wdmg\votes\Bootstrap',
            'moduleOptions' => [
                'routePrefix' => 'admin'
            ],
        ],
    ];

    public function bootstrap($app)
    {
        // Get the module instance
        $module = Yii::$app->getModule('admin');

        // Get URL path prefix if exist
        if (isset($module->routePrefix)) {
            $app->getUrlManager()->enableStrictParsing = true;
            $prefix = $module->routePrefix . '/';
        } else {
            $prefix = '';
        }

        // Add module URL rules
        $app->getUrlManager()->enablePrettyUrl = true;
        $app->getUrlManager()->showScriptName = false;
        $app->getUrlManager()->enableStrictParsing = true;
        $app->getUrlManager()->suffix = '/';
        $app->getUrlManager()->addRules(
            [
                '/admin' => 'admin/dashboard/index',
                '<module:admin>/<controller:\w+>' => '<module>/<controller>',
                '<module:admin>/<submodule:\w+>/<controller:\w+>' => '<module>/<submodule>/<controller>',
                '<module:admin>/<controller:\w+>/<action:\w+>' => '<module>/<controller>/<action>',
                '<module:admin>/<submodule:\w+>/<controller:\w+>/<action:\w+>' => '<module>/<submodule>/<controller>/<action>',
                /*[
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
                ],*/
            ],
            true
        );

        // Configure administrative panel
        $app->setComponents([
            'admin' => [
                'class' => 'wdmg\admin\components\Dashboard'
            ]
        ]);

        // Loading all modules
        if (!Yii::$app instanceof \yii\console\Application) {
            $extensions = $module->module->extensions;
            foreach ($extensions as $extension) {
                if (array_key_exists($extension['name'], $this->packages)) {

                    $package = (object)$this->packages[$extension['name']];
                    $module->setModule($package->moduleId, ArrayHelper::merge([
                    //Yii::$app->setModule($package->moduleId, ArrayHelper::merge([
                        'class' => $package->moduleClass
                    ], $package->moduleOptions));

                    $installed = Yii::$app->getModule($package->moduleId);
                    if ($installed) {

                        // Configure dashboard
                        $installed->layout = 'dashboard';
                        $installed->layoutPath = '@wdmg/admin/views/layouts';
                        //$installed->routePrefix = '';

                        if ($installed instanceof yii\base\BootstrapInterface)
                            $installed->bootstrap(Yii::$app);
                        else
                            Yii::$app->bootstrap[] = $package->bootstrapClass;
                    }
                }
            }
        }


        //$module->layoutPath = '';
    }
}