<?php

namespace wdmg\admin;

/**
 * @author          Alexsander Vyshnyvetskyy <alex.vyshnyvetskyy@gmail.com>
 * @copyright       Copyright (c) 2019 W.D.M.Group, Ukraine
 * @license         https://opensource.org/licenses/MIT Massachusetts Institute of Technology (MIT) License
 */

use yii\base\BootstrapInterface;
use Yii;


class Bootstrap implements BootstrapInterface
{

    public $modules = [
        "wdmg/yii2-activity",
        "wdmg/yii2-api",
        "wdmg/yii2-bookmarks",
        "wdmg/yii2-comments",
        "wdmg/yii2-forms",
        "wdmg/yii2-geo",
        "wdmg/yii2-likes",
        "wdmg/yii2-messages",
        "wdmg/yii2-options",
        "wdmg/yii2-rbac",
        "wdmg/yii2-reposts",
        "wdmg/yii2-reviews",
        "wdmg/yii2-services",
        "wdmg/yii2-stats",
        "wdmg/yii2-tasks",
        "wdmg/yii2-tickets",
        "wdmg/yii2-users",
        "wdmg/yii2-views",
        "wdmg/yii2-votes",
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
        $app->getUrlManager()->addRules(
            [
                $prefix . '<module:admin>' => '<module>/admin/index',
                $prefix . '<module:admin>/<controller:\w+>' => '<module>/<controller>',
                $prefix . '<module:admin>/<controller:\w+>/<action:[0-9a-zA-Z_\-]+>' => '<module>/<controller>/<action>',
                $prefix . '<module:admin>/<controller:\w+>/<action:[0-9a-zA-Z_\-]+>/<id:\d+>' => '<module>/<controller>/<action>',
                [
                    'pattern' => $prefix . '<module:admin>/',
                    'route' => '<module>/admin/index',
                    'suffix' => ''
                ], [
                    'pattern' => $prefix . '<module:admin>/<controller:\w+>/',
                    'route' => '<module>/<controller>',
                    'suffix' => ''
                ], [
                    'pattern' => $prefix . '<module:admin>/<controller:\w+>/<action:[0-9a-zA-Z_\-]+>/',
                    'route' => '<module>/<controller>/<action>',
                    'suffix' => ''
                ], [
                    'pattern' => $prefix . '<module:admin>/<controller:\w+>/<action:[0-9a-zA-Z_\-]+>/<id:\d+>/',
                    'route' => '<module>/<controller>/<action>',
                    'suffix' => ''
                ]
            ],
            false
        );

        // Configure administrative panel
        $app->setComponents([
            'admin' => [
                'class' => 'wdmg\admin\components\Dashboard'
            ]
        ]);



        $module = Yii::$app->getModule('admin');
        $extensions = $module->module->extensions;
        foreach ($extensions as $extension) {
            if (in_array($extension['name'], $this->installed)) {
                $module_id = substr($extension['name'], strpos($extension['name'], '-') + 1, strlen($extension['name']));
                if (!empty($module_id)) {
                    Yii::$app->setModule($module_id, [
                        'class' => str_replace('Bootstrap','Module', $extension['bootstrap']),
                        'routePrefix' => 'admin'
                    ]);
                    if (Yii::$app->hasModule($module_id)) {
                        if ($module instanceof yii\base\BootstrapInterface)
                            $module->bootstrap(Yii::$app);
                        else
                            Yii::$app->bootstrap[] = $extension['bootstrap'];
                    }
                }
            }
        }
    }
}