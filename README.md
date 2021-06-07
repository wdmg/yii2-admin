[![Yii2](https://img.shields.io/badge/required-Yii2_v2.0.40-blue.svg)](https://packagist.org/packages/yiisoft/yii2)
[![Downloads](https://img.shields.io/packagist/dt/wdmg/yii2-admin.svg)](https://packagist.org/packages/wdmg/yii2-admin)
[![Packagist Version](https://img.shields.io/packagist/v/wdmg/yii2-admin.svg)](https://packagist.org/packages/wdmg/yii2-admin)
![Progress](https://img.shields.io/badge/progress-ready_to_use-green.svg)
[![GitHub license](https://img.shields.io/github/license/wdmg/yii2-admin.svg)](https://github.com/wdmg/yii2-admin/blob/master/LICENSE)

<img src="./docs/images/yii2-admin.png" width="100%" alt="Administrative panel for Butterfly.CMS" />

# Yii2 Admin Module
Administrative panel for [Butterfly.CMS](https://github.com/wdmg/butterfly.cms).
                                                                                                     
This module is an integral part of the [Butterfly.СMS](https://butterflycms.com/) content management system, but can also be used as an standalone extension.

Copyrights (c) 2019-2021 [W.D.M.Group, Ukraine](https://wdmg.com.ua/)

# Requirements 
* PHP 5.6 or higher
* Yii2 v.2.0.40 and newest
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
            'cacheExpire' => 3600, // integer, the time to expire cache
            'multiSignIn' => false, // not allow by default
            'sessionTimeout' => 900, // 15 min.
            'customLocales' => [ // expanding the list of language locales for searching translations
                'uk-UA' => 'Українська',
            ],
            'customSupportModules' => [ // expanding the list of modules available for installation and download
                'wdmg/yii2-example',
            ],
            'customSidebarMenu' => [ // extending the sidebar menu list
                [
                    'label' => 'Example',
                    'icon' => 'fa fa-fw fa-bars',
                    'url' => ['/admin/example/default'],
                    'order' => 10,
                ]
            ],
            'customCreateMenu' => [ // expanding the creation menu list
                'wdmg/yii2-example' => [
                    'label' => 'Add new example',
                    'url' => ['/admin/example/default/create']
                ]
            ],
            'useSphinxSearch' => true, // boolean, the flag for configuration Sphinx Search
            'sphinxSearchConf' => [ // configuration of Sphinx Search daemon
                'dsn' => "mysql",
                'host' => "127.0.0.1",
                'port' => "9306",
                'username' => "",
                'password' => "",
            ]
        ],
        ...
    ],

# Routing

| Link to route (without prettyurl)                     | Link to route (prettyurl enabled)     | Description                          |
|:----------------------------------------------------- |:------------------------------------- |:------------------------------------ |
| http://example.com/index.php?r=admin                  | http://example.com/admin              | Main dashboard                       |
| http://example.com/index.php?r=admin/admin/login      | http://example.com/admin/login        | Auth to dashboard                    |
| http://example.com/index.php?r=admin/admin/restore    | http://example.com/admin/restore      | Restore access password              |

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
* v.1.3.2 - Free disk space info, memory limit fixed
* v.1.3.1 - Sphinx Search configuration and support
* v.1.3.0 - Multiple Sign In and session timeout, added counter widgets, expanding list language locales, modules and dashboard menu
* v.1.2.1 - Hot keys for pagination, support for Menu module
* v.1.2.0 - Gulp workflow. Process info refactoring, `phpinfo()` in modal
* v.1.1.28 - Fixed jQuery version on v3.5.1, rebuild assets
* v.1.1.27 - Support for Guard module, active processes info
* v.1.1.26 - Set error handler, fixed add/activate module in controller
* v.1.1.25 - Update dependencies, README.md
* v.1.1.24 - UrlManager rules fixed, support for Robots.txt module
* v.1.1.23 - Subclasses for buttons, stats widgets
* v.1.1.22 - Added pagination, DB status to System info