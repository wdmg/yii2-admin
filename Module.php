<?php

namespace wdmg\admin;

/**
 * Admin dashboard for Butterfly.CMS
 *
 * @category        Module
 * @version         1.4.3
 * @author          Alexsander Vyshnyvetskyy <alex.vyshnyvetskyy@gmail.com>
 * @link            https://github.com/wdmg/yii2-admin
 * @copyright       Copyright (c) 2019 - 2023 W.D.M.Group, Ukraine
 * @license         https://opensource.org/licenses/MIT Massachusetts Institute of Technology (MIT) License
 *
 */

use Yii;
use wdmg\base\BaseModule;
use wdmg\helpers\ArrayHelper;

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
    public $defaultRoute = 'admin/index';

    /**
     * @var string, the name of module
     */
    public $name = "Dashboard";

    /**
     * @var string, the description of module
     */
    public $description = "Main administrative panel";

	/**
	 * @var array, the list of support locales for multi-language versions of content.
	 * @note This variable will be override if you use the `wdmg\yii2-translations` module.
	 */
	public $supportLocales = ['ru-RU', 'uk-UA', 'en-US'];

    /**
     * @var boolean, the flag if updates check turn on
     */
    public $checkForUpdates = false;

	public $showDate = true;
    public $showTime = true;
    public $timeFormat24 = true;

    /**
     * @var integer, the time to expire cache
     */
    public $cacheExpire = 3600;
    public $rememberDuration = 86400;
    public $resetTokenExpire = 3600;
    public $supportEmail = 'noreply@example.com';

    /**
     * @var array, expanding the list of language locales for searching translations
     */
    public $customLocales = [];

    /**
     * @var array, expanding the list of modules available for installation and download
     */
    public $customSupportModules = [];

    /**
     * @var array, extending the sidebar menu list
     */
    public $customSidebarMenu = [];

    /**
     * @var array, expanding the creation menu list
     */
    public $customCreateMenu = [];

    /**
     * @var string the module version
     */
    private $version = "1.4.3";

    /**
     * @var integer, priority of initialization
     */
    private $priority = 0;

    /**
     * @var array, support system languages
     */
    private $locales = [
        'en-US' => 'English',
        'ru-RU' => 'Русский',
    ];

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
        'wdmg/yii2-content',
        'wdmg/yii2-options',
        'wdmg/yii2-catalog',
        'wdmg/yii2-store',
        'wdmg/yii2-billing',
        'wdmg/yii2-menu',
        'wdmg/yii2-pages',
        'wdmg/yii2-news',
        'wdmg/yii2-blog',
        'wdmg/yii2-rbac',
        'wdmg/yii2-reposts',
        'wdmg/yii2-reviews',
        'wdmg/yii2-services',
        'wdmg/yii2-stats',
        'wdmg/yii2-guard',
        'wdmg/yii2-media',
        'wdmg/yii2-redirects',
        'wdmg/yii2-tasks',
        'wdmg/yii2-search',
        'wdmg/yii2-tickets',
        'wdmg/yii2-sitemap',
        'wdmg/yii2-rss',
        'wdmg/yii2-turbo',
        'wdmg/yii2-amp',
        'wdmg/yii2-translations',
        'wdmg/yii2-subscribers',
        'wdmg/yii2-newsletters',
        'wdmg/yii2-terminal',
        'wdmg/yii2-robots',
        'wdmg/yii2-users',
        'wdmg/yii2-profiles',
        'wdmg/yii2-views',
        'wdmg/yii2-votes',
    ];

    /**
     * @var array of main menu items
     */
    private $menu = [
        [
            'label' => 'Dashboard',
            'icon' => 'fa fa-fw fa-tachometer-alt',
            'url' => ['/admin/admin/index'],
            'order' => 0,
        ], [
            'label' => 'Modules',
            'icon' => 'fa fa-fw fa-puzzle-piece',
            'url' => ['/admin/admin/modules'],
            'order' => 1,
        ], [
            'label' => 'System',
            'icon' => 'fa fa-fw fa-cogs',
            'items' => [
                'activity',
                'api',
                'options',
                'services',
                'redirects',
                'robots',
                'mailer',
                [
                    'label' => 'Information',
                    'icon' => 'fa fa-fw fa-info-circle',
                    'url' => ['/admin/admin/info'],
                    'order' => 99,
                ],
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
            'icon' => 'fa fa-fw fa-users',
            'items' => ['users', 'profiles', 'rbac'],
            'order' => 14,
        ], [
            'label' => 'Content',
            'icon' => 'fa fa-fw fa-archive',
            'items' => ['pages', 'media', 'content'],
            'order' => 7,
        ], [
            'label' => 'Publications',
            'icon' => 'fa fa-fw fa-pencil-alt',
            'items' => [
                'news',
                'blog',
                'subscribers',
                'newsletters'
            ],
            'order' => 8,
        ], [
            'label' => 'E-commerce',
            'icon' => 'fa fa-fw fa-shopping-bag',
            'items' => ['catalog', 'store', 'billing'],
            'order' => 9,
        ], [
            'label' => 'Feedbacks',
            'icon' => 'fa fa-fw fa-comments',
            'items' => ['reviews', 'comments', 'forms'],
            'order' => 10,
        ], [
            'label' => 'Socials',
            'icon' => 'fa fa-fw fa-share-alt',
            'items' => ['messages', 'likes', 'bookmarks', 'reposts', 'views', 'votes'],
            'order' => 11,
        ], [
            'label' => 'Security',
            'icon' => 'fa fa-fw fa-shield-alt',
            'item' => 'guard',
            'order' => 12,
        ], [
            'label' => 'Common',
            'icon' => 'fa fa-fw fa-wrench',
            'items' => ['menu', 'search', 'geo', 'translations', 'rss', 'turbo', 'amp', 'sitemap'],
            'order' => 13,
        ], [
            'label' => 'Stats',
            'icon' => 'fa fa-fw fa-chart-pie',
            'item' => 'stats',
            'order' => 14,
        ], [
            'label' => 'Support',
            'icon' => 'fa fa-fw fa-life-ring',
            'items' => ['tasks', 'tickets'],
            'order' => 15,
        ]
    ];

    /**
     * @var array of create menu items
     */
    private $createMenu = [
        'wdmg/yii2-pages' => [
            'label' => 'Page',
            'url' => ['/admin/pages/pages/create']
        ], 'wdmg/yii2-media' => [
            'label' => 'Media item',
            'url' => ['/admin/pages/pages/create']
        ], 'wdmg/yii2-content' => [
            [
                'label' => 'Content block',
                'url' => ['/admin/content/blocks/create']
            ],[
                'label' => 'Content list',
                'url' => ['/admin/content/lists/create']
            ],
        ], 'wdmg/yii2-menu' => [
            'label' => 'Menu item',
            'url' => ['/admin/menu/list/create'],
        ], 'wdmg/yii2-news' => [
            'label' => 'News',
            'url' => ['/admin/news/news/create']
        ], 'wdmg/yii2-blog' => [
            'label' => 'Post',
            'url' => ['/admin/blog/posts/create']
        ], 'wdmg/yii2-subscribers' => [
            'label' => 'Subscriber',
            'url' => ['/admin/subscribers/all/create']
        ], 'wdmg/yii2-newsletters' => [
            'label' => 'Newsletter',
            'url' => ['/admin/newsletters/list/create']
        ], 'wdmg/yii2-forms' => [
            'label' => 'Form',
            'url' => ['/admin/forms/list/create']
        ], 'wdmg/yii2-users' => [
            'label' => 'User',
            'url' => ['/admin/users/users/create/']
        ], 'wdmg/yii2-profiles' => [
            'label' => 'Profile',
            'url' => ['/admin/profiles/profiles/create/']
        ], 'wdmg/yii2-tasks' => [
            'label' => 'Task',
            'url' => ['/admin/tasks/item/create/']
        ], 'wdmg/yii2-translations' => [
            'label' => 'Translate',
            'url' => ['/admin/translations/list/create/']
        ],
    ];

    /**
     * @var bool, of show disabled menu items in dashboard
     */
    public $menuShowDisabled = false;

    /**
     * @var bool, of allow to multi Sign In
     */
    public $multiSignIn = false; // not allow by default

    /**
     * @var integer, session timeout in sec. of auth (where `0` is unlimited)
     */
    public $sessionTimeout = 60 * 15; // 15 min.

    /**
     * @var bool, the flag for configuration Sphinx Search
     */
    public $useSphinxSearch = true;

    /**
     * @var array, configuration of Sphinx Search daemon
     */
    public $sphinxSearchConf = [
        'dsn' => "mysql",
        'host' => "127.0.0.1",
        'port' => "9306",
        'username' => "root",
        'password' => "root",
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
            Yii::$app->user->loginUrl = ['admin/login'];

            // Set assets bundle, if not loaded
            if ($this->isBackend() && !$this->isConsole()) {

                if (!isset(Yii::$app->assetManager->bundles['wdmg\admin\AdminAsset']))
                    Yii::$app->assetManager->bundles['wdmg\admin\AdminAsset'] = \wdmg\admin\AdminAsset::register(Yii::$app->view);

                if (!isset(Yii::$app->assetManager->bundles['wdmg\admin\FontAwesomeAssets']))
                    Yii::$app->assetManager->bundles['wdmg\admin\FontAwesomeAssets'] = \wdmg\admin\FontAwesomeAssets::register(Yii::$app->view);

            }

            // Check of updates and return current version
            $meta = $this->getMetaData();
            $version = $this->getVersion();
            if ($new_version = $this->checkUpdates($meta['name'], $version)) // Check of updates for `wdmg/yii2-admin`
                $this->view->params['version'] = 'v'. $version . ' <label class="label label-danger">Available update to ' . $new_version . '</label>';
            else
                $this->view->params['version'] = 'v'. $version;

	        $this->showDate = $this->getOption('admin.showDate');
	        $this->showTime = $this->getOption('admin.showTime');
	        $this->timeFormat24 = $this->getOption('admin.timeFormat24');

	        $this->view->params['datetime.showDate'] = $this->showDate;
	        $this->view->params['datetime.showTime'] = $this->showTime;
	        $this->view->params['datetime.timeFormat24'] = $this->timeFormat24;
        }

    }

    /**
     * Return list of support languages
     * @return array of locales
     */
    public function getSupportLanguages()
    {
        return ArrayHelper::merge((is_array($this->customLocales) ? $this->customLocales : []), $this->locales);
    }

    /**
     * Return list of support modules
     * @return array of modules vendor/name
     */
    public function getSupportModules()
    {
        return ArrayHelper::merge((is_array($this->customSupportModules) ? $this->customSupportModules : []), $this->support);
    }

    /**
     * Return list of dashboard main menu items
     * @return array
     */
    public function getMenuItems()
    {
        return ArrayHelper::merge((is_array($this->customSidebarMenu) ? $this->customSidebarMenu : []), $this->menu);
    }

    /**
     * Return list of dashboard create menu items
     * @return array
     */
    public function getCreateMenuItems()
    {
        return ArrayHelper::merge((is_array($this->customCreateMenu) ? $this->customCreateMenu : []), $this->createMenu);
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

        $status = null;
        $versions = null;
        $remote_version = null;
        if (Yii::$app->getCache()) {
            if (Yii::$app->cache->exists('modules.versions'))
                $versions = Yii::$app->cache->get('modules.versions');

            if (Yii::$app->cache->exists('modules.updates'))
                $status = Yii::$app->cache->get('modules.updates');
        }
        if (isset($versions[$module_name]))
            $remote_version = $versions[$module_name];

        $update_server = 'https://api.github.com';
        if (is_null($remote_version) && !$status == 'sleep' && @get_headers($update_server)) {

            $client = new \yii\httpclient\Client(['baseUrl' => $update_server]);
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

                    if (Yii::$app->getCache())
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
                    if (Yii::$app->getCache())
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