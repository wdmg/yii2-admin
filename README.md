[![Yii2](https://img.shields.io/badge/required-Yii2_v2.0.20-blue.svg)](https://packagist.org/packages/yiisoft/yii2)
[![Github all releases](https://img.shields.io/github/downloads/wdmg/yii2-admin/total.svg)](https://GitHub.com/wdmg/yii2-admin/releases/)
![Progress](https://img.shields.io/badge/progress-ready_to_use-green.svg)
[![GitHub license](https://img.shields.io/github/license/wdmg/yii2-admin.svg)](https://github.com/wdmg/yii2-admin/blob/master/LICENSE)
![GitHub release](https://img.shields.io/github/release/wdmg/yii2-admin/all.svg)

# Yii2 Admin Module
Administrative panel for Butterfly.CMS

# Requirements 
* PHP 5.6 or higher
* Yii2 v.2.0.20 and newest
* [Yii2 Base](https://github.com/wdmg/yii2-base) module (required)
* [Yii2 Users](https://github.com/wdmg/yii2-users) module (required)

# Installation
To install the module, run the following command in the console:

`$ composer require "wdmg/yii2-admin"`

After configure db connection, run the following command in the console:

`$ php yii admin/init`

And select the operation you want to perform:
  1) Apply all module migrations
  2) Revert all module migrations

# Migrations
In any case, you can execute the migration and create the initial data, run the following command in the console:

`$ php yii migrate --migrationPath=@vendor/wdmg/yii2-admin/migrations`

# Configure

To add a module to the project, add the following data in your configuration file:

    'modules' => [
        ...
        'admin' => [
            'class' => 'wdmg\admin\Module',
            'routePrefix' => 'admin'
        ],
        ...
    ],

# Routing
Use the `Module::dashboardNavItems()` method of the module to generate a navigation items list for NavBar, like this:

    <?php
        echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-right'],
            'label' => 'Modules',
            'items' => [
                Yii::$app->getModule('admin')->dashboardNavItems(),
                ...
            ]
        ]);
    ?>

# Status and version [ready to use]
* v.1.0.9 - Rebuild assets
* v.1.0.8 - Added restore and reset password functionality
* v.1.0.7 - Module assets and login form
* v.1.0.6 - Added admin assets bundle