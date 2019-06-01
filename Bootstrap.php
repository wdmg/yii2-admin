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
    }
}