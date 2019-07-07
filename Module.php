<?php

namespace wdmg\admin;

/**
 * Yii2 Admin panel for Butterfly.CMS
 *
 * @category        Module
 * @version         1.0.9
 * @author          Alexsander Vyshnyvetskyy <alex.vyshnyvetskyy@gmail.com>
 * @link            https://github.com/wdmg/yii2-admin
 * @copyright       Copyright (c) 2019 W.D.M.Group, Ukraine
 * @license         https://opensource.org/licenses/MIT Massachusetts Institute of Technology (MIT) License
 *
 */

use Yii;
use wdmg\base\BaseModule;

/**
 * api module definition class
 */
class Module extends BaseModule
{
    /**
     * {@inheritdoc}
     */
    public $controllerNamespace = 'wdmg\admin\controllers';

    /**
     * {@inheritdoc}
     */
    public $defaultRoute = 'dashboard/index';

    /**
     * @var string, the name of module
     */
    public $name = "Dashboard";

    /**
     * @var string, the description of module
     */
    public $description = "Main administrative panel";

    /**
     * @var string the module version
     */
    private $version = "1.0.9";

    /**
     * @var integer, priority of initialization
     */
    private $priority = 1;

    /**
     * @var array of support modules
     */
    private $support = [
        'wdmg/yii2-activity',
        'wdmg/yii2-api',
        'wdmg/yii2-bookmarks',
        'wdmg/yii2-comments',
        'wdmg/yii2-forms',
        'wdmg/yii2-geo',
        'wdmg/yii2-likes',
        'wdmg/yii2-messages',
        'wdmg/yii2-options',
        'wdmg/yii2-rbac',
        'wdmg/yii2-reposts',
        'wdmg/yii2-reviews',
        'wdmg/yii2-services',
        'wdmg/yii2-stats',
        'wdmg/yii2-redirects',
        'wdmg/yii2-tasks',
        'wdmg/yii2-tickets',
        'wdmg/yii2-translations',
        'wdmg/yii2-users',
        'wdmg/yii2-views',
        'wdmg/yii2-votes',
    ];

    /**
     * @var array of modules
     */
    public $packages = [
        "wdmg/yii2-stats" => [
            'moduleId' => 'stats',
            'moduleClass' => 'wdmg\stats\Module',
            'bootstrapClass' => null,
            'moduleOptions' => [
                'routePrefix' => 'admin',
                'ignoreDev' => false,
                'ignoreRoute' => [],
                'ignoreListIp' => []
            ],
        ],
        "wdmg/yii2-redirects" => [
            'moduleId' => 'redirects',
            'moduleClass' => 'wdmg\redirects\Module',
            'bootstrapClass' => null,
            'moduleOptions' => [
                'autocheck' => true,
                'routePrefix' => 'admin'
            ],
        ],
        "wdmg/yii2-activity" => [
            'moduleId' => 'activity',
            'moduleClass' => 'wdmg\activity\Module',
            'bootstrapClass' => null,
            'moduleOptions' => [
                'routePrefix' => 'admin'
            ],
        ],
        "wdmg/yii2-api" => [
            'moduleId' => 'api',
            'moduleClass' => 'wdmg\api\Module',
            'bootstrapClass' => null,
            'moduleOptions' => [
                'routePrefix' => 'admin'
            ],
        ],
        "wdmg/yii2-bookmarks" => [
            'moduleId' => 'bookmarks',
            'moduleClass' => 'wdmg\bookmarks\Module',
            'bootstrapClass' => null,
            'moduleOptions' => [
                'routePrefix' => 'admin'
            ],
        ],
        "wdmg/yii2-comments" => [
            'moduleId' => 'comments',
            'moduleClass' => 'wdmg\comments\Module',
            'bootstrapClass' => null,
            'moduleOptions' => [
                'routePrefix' => 'admin'
            ],
        ],
        "wdmg/yii2-forms" => [
            'moduleId' => 'forms',
            'moduleClass' => 'wdmg\forms\Module',
            'bootstrapClass' => null,
            'moduleOptions' => [
                'routePrefix' => 'admin'
            ],
        ],
        "wdmg/yii2-geo" => [
            'moduleId' => 'geo',
            'moduleClass' => 'wdmg\geo\Module',
            'bootstrapClass' => null,
            'moduleOptions' => [
                'routePrefix' => 'admin'
            ],
        ],
        "wdmg/yii2-likes" => [
            'moduleId' => 'likes',
            'moduleClass' => 'wdmg\likes\Module',
            'bootstrapClass' => null,
            'moduleOptions' => [
                'routePrefix' => 'admin'
            ],
        ],
        "wdmg/yii2-messages" => [
            'moduleId' => 'messages',
            'moduleClass' => 'wdmg\messages\Module',
            'bootstrapClass' => null,
            'moduleOptions' => [
                'routePrefix' => 'admin'
            ],
        ],
        "wdmg/yii2-options" => [
            'moduleId' => 'options',
            'moduleClass' => 'wdmg\options\Module',
            'bootstrapClass' => null,
            'moduleOptions' => [
                'routePrefix' => 'admin'
            ],
        ],
        "wdmg/yii2-rbac" => [
            'moduleId' => 'rbac',
            'moduleClass' => 'wdmg\rbac\Module',
            'bootstrapClass' => null,
            'moduleOptions' => [
                'routePrefix' => 'admin'
            ],
        ],
        "wdmg/yii2-reposts" => [
            'moduleId' => 'reposts',
            'moduleClass' => 'wdmg\reposts\Module',
            'bootstrapClass' => null,
            'moduleOptions' => [
                'routePrefix' => 'admin'
            ],
        ],
        "wdmg/yii2-reviews" => [
            'moduleId' => 'reviews',
            'moduleClass' => 'wdmg\reviews\Module',
            'bootstrapClass' => null,
            'moduleOptions' => [
                'routePrefix' => 'admin'
            ],
        ],
        "wdmg/yii2-services" => [
            'moduleId' => 'services',
            'moduleClass' => 'wdmg\services\Module',
            'bootstrapClass' => null,
            'moduleOptions' => [
                'routePrefix' => 'admin'
            ],
        ],
        "wdmg/yii2-tasks" => [
            'moduleId' => 'tasks',
            'moduleClass' => 'wdmg\tasks\Module',
            'bootstrapClass' => null,
            'moduleOptions' => [
                'routePrefix' => 'admin'
            ],
        ],
        "wdmg/yii2-tickets" => [
            'moduleId' => 'tickets',
            'moduleClass' => 'wdmg\tickets\Module',
            'bootstrapClass' => null,
            'moduleOptions' => [
                'routePrefix' => 'admin'
            ],
        ],
        "wdmg/yii2-users" => [
            'moduleId' => 'users',
            'moduleClass' => 'wdmg\users\Module',
            'bootstrapClass' => null,
            'moduleOptions' => [
                'routePrefix' => 'admin'
            ],
        ],
        "wdmg/yii2-views" => [
            'moduleId' => 'views',
            'moduleClass' => 'wdmg\views\Module',
            'bootstrapClass' => null,
            'moduleOptions' => [
                'routePrefix' => 'admin'
            ],
        ],
        "wdmg/yii2-votes" => [
            'moduleId' => 'votes',
            'moduleClass' => 'wdmg\votes\Module',
            'bootstrapClass' => null,
            'moduleOptions' => [
                'routePrefix' => 'admin'
            ],
        ]
    ];


    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        // Set version of current module
        $this->setVersion($this->version);

        // Set priority of current module
        $this->setPriority($this->priority);

        if (!Yii::$app instanceof \yii\console\Application) {
            // Set authorization route
            Yii::$app->user->loginUrl = ['/admin/login'];

            // Set assets bundle, if not loaded
            if(!isset(Yii::$app->assetManager->bundles['wdmg\admin\AdminAsset']))
                Yii::$app->assetManager->bundles['wdmg\admin\AdminAsset'] = \wdmg\admin\AdminAsset::register(Yii::$app->view);
        }
    }

    /**
     * Return list of support modules
     * @return array of modules vendor/name
     */
    public function getSupportModules()
    {
        return $this->support;
    }
}