<?php

namespace wdmg\admin;

/**
 * Yii2 Admin panel for Butterfly.CMS
 *
 * @category        Module
 * @version         1.1.8
 * @author          Alexsander Vyshnyvetskyy <alex.vyshnyvetskyy@gmail.com>
 * @link            https://github.com/wdmg/yii2-admin
 * @copyright       Copyright (c) 2019 W.D.M.Group, Ukraine
 * @license         https://opensource.org/licenses/MIT Massachusetts Institute of Technology (MIT) License
 *
 */

use Yii;
use wdmg\base\BaseModule;
use yii\helpers\ArrayHelper;

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
     * @var boolean, the flag if updates check turn on
     */
    public $checkForUpdates = true;

    /**
     * @var integer, the time to expire cache
     */
    public $cacheExpire = 3600;

    /**
     * @var string the module version
     */
    private $version = "1.1.8";

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
        'wdmg/yii2-mailer',
        'wdmg/yii2-options',
        'wdmg/yii2-pages',
        'wdmg/yii2-news',
        'wdmg/yii2-rbac',
        'wdmg/yii2-reposts',
        'wdmg/yii2-reviews',
        'wdmg/yii2-services',
        'wdmg/yii2-stats',
        'wdmg/yii2-redirects',
        'wdmg/yii2-tasks',
        'wdmg/yii2-tickets',
        'wdmg/yii2-translations',
        'wdmg/yii2-terminal',
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
                'mailer'
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
            'label' => 'Content',
            'icon' => 'fa-archive',
            'items' => ['pages', 'media'],
            'order' => 7,
        ], [
            'label' => 'Publications',
            'icon' => 'fa-pencil-square-o',
            'items' => [
                'news',
                /*[
                    'label' => 'Subscribers',
                    'icon' => 'fa-newspaper-o'
                ],
                [
                    'label' => 'Newsletters',
                    'icon' => 'fa-envelope'
                ],*/
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

        // Check of updates and return current version
        $meta = $this->getMetaData();
        $version = $this->getVersion();
        if ($new_version = $this->checkUpdates($meta['name'], $version)) // wdmg/yii2-admin
            $this->view->params['version'] = 'v'. $version . ' <label class="label label-danger">Available update to ' . $new_version . '</label>';
        else
            $this->view->params['version'] = 'v'. $version;

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


    /**
     * Check for available updates
     * @return string, remote version
     */
    public function checkUpdates($module_name, $current_version)
    {
        $viewed = array();
        $session = Yii::$app->session;
        $check_updates = $this->getOption('admin.checkForUpdates');

        if(isset($session['viewed-flash']) && is_array($session['viewed-flash']))
            $viewed = $session['viewed-flash'];

        // Get time to expire cache
        if (isset(Yii::$app->params['admin.cacheExpire']))
            $expire = intval(Yii::$app->params['admin.cacheExpire']);
        else
            $expire = $this->cacheExpire;

        if (!$check_updates) {
            Yii::warning('Attention! In the system settings, the ability to check for updates is disabled.', __METHOD__);
            if(!in_array('admin-check-updates', $viewed) && is_array($viewed)) {
                Yii::$app->getSession()->setFlash(
                    'warning',
                    Yii::t('app/modules/admin', 'Attention! In the system settings, the ability to check for updates is disabled.')
                );
                $session['viewed-flash'] = array_merge(array_unique($viewed), ['admin-check-updates']);
            }
            return false;
        }

        if (Yii::$app->user->isGuest || !$module_name || !$current_version)
            return false;

        $remote_version = null;
        $versions = Yii::$app->cache->get('modules.versions');
        $status = Yii::$app->cache->get('modules.updates');

        if (isset($versions[$module_name]))
            $remote_version = $versions[$module_name];

        if (is_null($remote_version) && !$status == 'sleep') {

            $client = new \yii\httpclient\Client(['baseUrl' => 'https://api.github.com']);
            $response = $client->get('/repos/'.$module_name.'/releases/latest', [])->setHeaders([
                'User-Agent' => 'Butterfly.CMS',
                'Content-Type' => 'application/json'
            ])->send();

            if ($response->getStatusCode() == 200) {
                $data = \json_decode($response->content);
                $remote_version = $data->tag_name;

                if ($remote_version && $versions)
                    Yii::$app->cache->add('modules.versions', ArrayHelper::merge($versions, [$module_name => $remote_version]), intval($expire));
                elseif ($remote_version)
                    Yii::$app->cache->add('modules.versions', [$module_name => $remote_version], intval($expire));

            } else {

                if ($response->getStatusCode() == 404) {
                    Yii::error('An error occurred while checking for updates for `'.$module_name.'`. 404 - Resource not found.', __METHOD__);
                    Yii::$app->session->setFlash(
                        'error',
                        Yii::t('app/modules/admin', 'An error occurred while checking for updates for `{module}`. 404 - Resource not found.',
                            ['module' => $module_name]
                        )
                    );
                } else if ($response->getStatusCode() == 403) {
                    Yii::$app->cache->add('modules.updates', 'sleep', intval($expire));
                    Yii::error('An error occurred while checking for updates to one or more modules. 403 - Request limit exceeded.', __METHOD__);
                    if(!in_array('admin-updates-limit', $viewed) && is_array($viewed)) {
                        Yii::$app->getSession()->setFlash(
                            'error',
                            Yii::t('app/modules/admin', 'An error occurred while checking for updates to one or more modules. 403 - Request limit exceeded.')
                        );
                        $session['viewed-flash'] = array_merge(array_unique($viewed), ['admin-updates-limit']);
                    }
                } else if ($response->getStatusCode() == 503) {
                    Yii::$app->cache->add('modules.updates', 'sleep', intval($expire));
                    Yii::error('An error occurred while checking for updates to one or more modules. 503 - Service is temporarily unavailable.', __METHOD__);
                    if(!in_array('admin-updates-unavailable', $viewed) && is_array($viewed)) {
                        Yii::$app->getSession()->setFlash(
                            'error',
                            Yii::t('app/modules/admin', 'An error occurred while checking for updates to one or more modules. 503 - Service is temporarily unavailable.')
                        );
                        $session['viewed-flash'] = array_merge(array_unique($viewed), ['admin-updates-unavailable']);
                    }
                }

                return false;
            }
        }

        if (!version_compare($remote_version, $current_version, '<='))
            return $remote_version;
        else
            return false;

    }

}