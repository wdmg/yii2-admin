<?php

namespace wdmg\admin;

/**
 * Yii2 Admin panel for Butterfly.CMS
 *
 * @category        Module
 * @version         1.1.1
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
    private $version = "1.1.1";

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
     * @var array of main menu items
     */
    private $menu = [
        [
            'label' => 'Dashboard',
            'icon' => 'fa-dashboard',
            'url' => '/admin/index',
            'order' => 1,
        ], [
            'label' => 'Modules',
            'icon' => 'fa-puzzle-piece',
            'url' => '/admin/modules',
            'order' => 1,
        ], [
            'label' => 'System',
            'icon' => 'fa-gears',
            'items' => [
                /*[
                    'label' => 'Modules',
                    'icon' => 'fa-check',
                    'order' => 3
                ],*/
                'activity',
                'api',
                'options',
                'services',
                'forms',
                'redirects',
            ],
            'order' => 2,
        ], /*[
            'label' => 'Access',
            'icon' => 'fa-lock',
            'order' => 3,
            'items' => [
                [
                    'label' => 'Some link3',
                    'icon' => 'fa-check',
                    'items' => ['geo', 'stats'],
                    'order' => 3
                ], [
                    'label' => 'Some link1',
                    'icon' => 'fa-check',
                    'order' => 1
                ], [
                    'label' => 'Some link2',
                    'icon' => 'fa-check',
                    'items' => ['users'],
                    'url' => 'fa-check',
                    'order' => 2
                ]
            ],
        ],*/ [
            'label' => 'Users',
            'icon' => 'fa-users',
            'items' => ['users', 'rbac'],
            'order' => 14,
        ], [
            'label' => 'Pages',
            'icon' => 'fa-folder',
            'order' => 5,
        ], [
            'label' => 'Content',
            'icon' => 'fa-archive',
            'items' => ['media'],
            'order' => 7,
        ], [
            'label' => 'Publications',
            'icon' => 'fa-pencil-square-o',
            'items' => [
                [
                    'label' => 'News',
                    'icon' => 'fa-newspaper-o'
                ],
                [
                    'label' => 'Subscribers',
                    'icon' => 'fa-newspaper-o'
                ],
                [
                    'label' => 'Newsletters',
                    'icon' => 'fa-envelope'
                ],
            ],
            'order' => 8,
        ], [
            'label' => 'E-commerce',
            'icon' => 'fa-shopping-bag',
            'order' => 9,
        ], [
            'label' => 'Feedbacks',
            'icon' => 'fa-comments',
            'items' => ['reviews', 'comments'],
            'order' => 10,
        ], [
            'label' => 'Socials',
            'icon' => 'fa-share-alt',
            'items' => ['messages', 'likes', 'bookmarks', 'reposts', 'views', 'votes'],
            'order' => 11,
        ], [
            'label' => 'Common',
            'icon' => 'fa-wrench',
            'items' => ['geo', 'translations'],
            'order' => 12,
        ], [
            'label' => 'Security',
            'icon' => 'fa-shield',
            'order' => 13,
        ], [
            'label' => 'Stats',
            'icon' => 'fa-pie-chart',
            'item' => 'stats',
            'order' => 14,
        ], [
            'label' => 'Support',
            'icon' => 'fa-support',
            'items' => ['tasks', 'tickets'],
            'order' => 15,
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

    /**
     * Return list of dashboard menu items
     * @return array
     */
    public function getMenuItems()
    {
        return $this->menu;
    }
}