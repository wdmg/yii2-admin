[![Yii2](https://img.shields.io/badge/required-Yii2_v2.0.33-blue.svg)](https://packagist.org/packages/yiisoft/yii2)
[![Downloads](https://img.shields.io/packagist/dt/wdmg/yii2-admin.svg)](https://packagist.org/packages/wdmg/yii2-admin)
[![Packagist Version](https://img.shields.io/packagist/v/wdmg/yii2-admin.svg)](https://packagist.org/packages/wdmg/yii2-admin)
![Progress](https://img.shields.io/badge/progress-ready_to_use-green.svg)
[![GitHub license](https://img.shields.io/github/license/wdmg/yii2-admin.svg)](https://github.com/wdmg/yii2-admin/blob/master/LICENSE)


# Yii2 Admin Module
Administrative panel for [Butterfly.CMS](https://github.com/wdmg/butterfly.cms)

# Requirements 
* PHP 5.6 or higher
* Yii2 v.2.0.33 and newest
* [Yii2 Base](https://github.com/wdmg/yii2-base) module (required)
* [Yii2 Users](https://github.com/wdmg/yii2-users) module (required)

# Installation
To install the module, run the following command in the console:

`$ composer require "wdmg/yii2-admin"`

After configure db connection, run the following command in the console:

`$ php yii admin/init`

And select the operation you want to perform:
  1) Apply all modules migrations
  2) Revert all modules migrations

# Migrations
In any case, you can execute the migration and create the initial data, run the following command in the console:

`$ php yii migrate --migrationPath=@vendor/wdmg/yii2-admin/migrations`

# Configure

To add a module to the project, add the following data in your configuration file:

    'modules' => [
        ...
        'admin' => [
            'class' => 'wdmg\admin\Module',
            'routePrefix' => 'admin',
            'checkForUpdates' => true, // boolean, the flag if updates check turn on
            'cacheExpire' => 3600 // integer, the time to expire cache
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
* v.1.1.22 - Added pagination, DB status to System info
* v.1.1.21 - Added support for Blog module
* v.1.1.20 - Added system information page
* v.1.1.19 - Added support for Search module, error handler for dashboard