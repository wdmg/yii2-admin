<?php

namespace wdmg\admin;

/**
 * Yii2 Admin panel for Butterfly.CMS
 *
 * @category        Module
 * @version         1.0.3
 * @author          Alexsander Vyshnyvetskyy <alex.vyshnyvetskyy@gmail.com>
 * @link            https://github.com/wdmg/yii2-admin
 * @copyright       Copyright (c) 2019 W.D.M.Group, Ukraine
 * @license         https://opensource.org/licenses/MIT Massachusetts Institute of Technology (MIT) License
 *
 */

use Yii;

/**
 * api module definition class
 */
class Module extends \yii\base\Module
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
     * @var string the prefix for routing of module
     */
    public $routePrefix = "admin";

    /**
     * @var string, the name of module
     */
    public $name = "Dashboard";

    /**
     * @var string, the description of module
     */
    public $description = "Main administrative panel";

    /**
     * @var string the vendor name of module
     */
    private $vendor = "wdmg";

    /**
     * @var string the module version
     */
    private $version = "1.0.3";

    /**
     * @var integer, priority of initialization
     */
    private $priority = 1;

    /**
     * @var array of strings missing translations
     */
    public $missingTranslation;


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

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        // Set controller namespace for console commands
        if (Yii::$app instanceof \yii\console\Application)
            $this->controllerNamespace = 'wdmg\admin\commands';

        // Set current version of module
        $this->setVersion($this->version);

        // Register translations
        $this->registerTranslations();

        // Normalize route prefix
        $this->routePrefixNormalize();
    }

    /**
     * Return module vendor
     * @var string of current module vendor
     */
    public function getVendor() {
        return $this->vendor;
    }

    /**
     * {@inheritdoc}
     */
    public function afterAction($action, $result)
    {

        // Log to debuf console missing translations
        if (is_array($this->missingTranslation) && YII_ENV == 'dev')
            Yii::warning('Missing translations: ' . var_export($this->missingTranslation, true), 'i18n');

        $result = parent::afterAction($action, $result);
        return $result;

    }

    // Registers translations for the module
    public function registerTranslations()
    {
        Yii::$app->i18n->translations['app/modules/admin'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'sourceLanguage' => 'en-US',
            'basePath' => '@vendor/wdmg/yii2-admin/messages',
            'on missingTranslation' => function($event) {

                if (YII_ENV == 'dev')
                    $this->missingTranslation[] = $event->message;

            },
        ];

        // Name and description translation of module
        $this->name = Yii::t('app/modules/admin', $this->name);
        $this->description = Yii::t('app/modules/admin', $this->description);
    }

    public static function t($category, $message, $params = [], $language = null)
    {
        return Yii::t('app/modules/admin' . $category, $message, $params, $language);
    }

    /**
     * Normalize route prefix
     * @return string of current route prefix
     */
    public function routePrefixNormalize()
    {
        if(!empty($this->routePrefix)) {
            $this->routePrefix = str_replace('/', '', $this->routePrefix);
            $this->routePrefix = '/'.$this->routePrefix;
            $this->routePrefix = str_replace('//', '/', $this->routePrefix);
        }
        return $this->routePrefix;
    }

    /**
     * Build dashboard navigation items for NavBar
     * @return array of current module nav items
     */
    public function dashboardNavItems()
    {
        return [
            'label' => $this->name,
            'url' => [$this->routePrefix . '/admin/'],
            'active' => in_array(\Yii::$app->controller->module->id, ['admin'])
        ];
    }
}