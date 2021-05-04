<?php

namespace wdmg\admin;

/**
 * @author          Alexsander Vyshnyvetskyy <alex.vyshnyvetskyy@gmail.com>
 * @copyright       Copyright (c) 2019 - 2021 W.D.M.Group, Ukraine
 * @license         https://opensource.org/licenses/MIT Massachusetts Institute of Technology (MIT) License
 */

use Yii;
use wdmg\base\BaseModule;
use yii\base\BootstrapInterface;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use yii\helpers\BaseFileHelper;
use wdmg\validators\SerialValidator;


class Bootstrap extends BaseModule implements BootstrapInterface
{

    private $_module;

    public function bootstrap($app)
    {
        // Get the module instance
        $this->_module = $app->getModule('admin', true);

        // Get translations module
        $translations = $app->getModule('admin/translations', false);

        if (YII_ENV_DEV)
            $app->getUrlManager()->cache = null;

        if ($this->isBackend()) {
            $app->getUrlManager()->addRules(
                [
                    '/admin' => 'admin/admin/index',
                    '/admin/<action:(index|modules|login|logout|restore|search|checkpoint|bugreport|info|error)>' => 'admin/admin/<action>',

                    '<module:\w+>/<submodule:\w+>/<controller:\w+>/<action:\w+>/<id:\d+>' => '<module>/<submodule>/<controller>/<action>',
                    '<module:\w+>/<controller:\w+>/<action:\w+>/<id:\d+>' => '<module>/<controller>/<action>',
                    '<module:\w+>/<submodule:\w+>/<controller:\w+>/<action:\w+>' => '<module>/<submodule>/<controller>/<action>',
                    '<module:\w+>/<controller:\w+>/<action:\w+>' => '<module>/<controller>/<action>',
                    '<module:\w+>/<submodule:\w+>/<controller:\w+>' => '<module>/<submodule>/<controller>/index',
                    //'<module:\w+>/<controller:\w+>' => '<module>/<controller>/index',
                    '<controller:\w+>/<action:\w+>' => '<controller>/<action>',
                    '<controller:\w+>' => '<controller>/index',
                ],
                true
            );
        } else {
            $app->getUrlManager()->addRules(
                [
                    '/admin' => 'admin/admin/index',
                    '/admin/<action:(index|modules|login|logout|restore|search|checkpoint|bugreport|info|error)>' => 'admin/admin/<action>',
                ],
                true
            );
        }

        // Register language of user interface
        if (!($app instanceof \yii\console\Application)) {

            // Set the error handler page
            if (!YII_ENV_TEST) {
                $errorHandler = Yii::$app->getErrorHandler();
                $errorHandler->errorAction = 'admin/admin/error';
            }

            if ($lang = $app->request->get('lang', false)) {
                $app->session->set('lang', $lang);
                $app->language = $lang;
                $app->response->cookies->add(new \yii\web\Cookie([
                    'name' => 'lang',
                    'value' => $lang,
                    'expire' => time() + 604800
                ]));
            } else {
                if ($lang = $app->session->get('lang', false)) {
                    $app->language = $lang;
                } else if ($lang = Yii::$app->request->cookies->getValue('lang', (isset($_COOKIE['lang'])) ? $_COOKIE['lang'] : false)) {
                    $app->language = $lang;
                }
            }
        }

        // Configure languages menu for UI
        if (!($app instanceof \yii\console\Application) && $this->_module) {
            \yii\base\Event::on(\yii\base\Controller::class, \yii\base\Controller::EVENT_BEFORE_ACTION, function ($event) use ($translations) {

                $langs = [];

                // Get custom locales from params
                if (isset(Yii::$app->params['admin.customLocales']))
                    $this->_module->customLocales = Yii::$app->params['admin.customLocales'];

                // Get custom list of support modules from params
                if (isset(Yii::$app->params['admin.customSupportModules']))
                    $this->_module->customSupportModules = Yii::$app->params['admin.customSupportModules'];

                // Get custom list of sidebar menu from params
                if (isset(Yii::$app->params['admin.customSidebarMenu']))
                    $this->_module->customSidebarMenu = Yii::$app->params['admin.customSidebarMenu'];

                // Get custom list of create menu from params
                if (isset(Yii::$app->params['admin.customCreateMenu']))
                    $this->_module->customCreateMenu = Yii::$app->params['admin.customCreateMenu'];

                $locales = $this->_module->getSupportLanguages();
                if ($translations) {

                    // Register translations for admin module
                    $translations->registerTranslations($this->_module, true);

                    $bundle = \wdmg\translations\FlagsAsset::register(Yii::$app->view);
                    foreach ($locales as $locale => $name) {

                        $locale = $translations->parseLocale($locale, Yii::$app->language);
                        if (!($country = $locale['domain']))
                            $country = '_unknown';

                        $flag = \yii\helpers\Html::img($bundle->baseUrl . '/flags-iso/flat/24/'.$country.'.png');

                        $langs[] = [
                            'label' => $flag . '&nbsp;' . $locale['name'],
                            'url' => \yii\helpers\Url::current(['lang' => $locale['locale']]),
                            'active'=> (Yii::$app->language == $locale['locale']) ? true : false,
                            'options' => [
                                'class' => (Yii::$app->language == $locale['locale']) ? 'active' : false
                            ],
                            'linkOptions' => [
                                'data' => [
                                    'label' => $name,
                                    'pjax' => 1
                                ]
                            ]
                        ];
                    }
                } else {

                    // Register translations for admin module
                    $this->_module->registerTranslations($this->_module);

                    foreach ($locales as $locale => $name) {
                        $langs[] = [
                            'label' => $name,
                            'url' => '?lang='.$locale,
                            'active'=> (Yii::$app->language == $locale) ? true : false,
                            'options' => [
                                'class' => (Yii::$app->language == $locale) ? 'active' : false
                            ],
                            'linkOptions' => [
                                'data' => [
                                    'label' => $name,
                                    'pjax' => 1
                                ]
                            ]
                        ];
                    }
                }

                Yii::$app->view->params['langs'] = $langs;
            });
        }

        // Configure administrative panel
        $app->setComponents([
            'dashboard' => [
                'class' => 'wdmg\admin\components\Dashboard'
            ]
        ]);

        // Loading all child modules
        if ($app->db->schema->getTableSchema(\wdmg\admin\models\Modules::tableName())) {
            $migrationLookup = [];
            $model = new \wdmg\admin\models\Modules();
            if ($modules = $model::getModules(true)) {
                if (is_array($modules)) {

                    foreach ($modules as $module) {

                        // Setup module ID
                        $moduleId = 'admin/' . $module['module'];

                        if (!class_exists($module['class'])) {
                            throw new InvalidConfigException('Can\'t load module `' . $module['class'] . '`');
                        } else {

                            // Check if this module has been loaded before
                            if (!$app->hasModule($moduleId)) {

                                // Get default module options
                                $options = [];
                                if (is_array($module['options'])) {
                                    $options = $module['options'];
                                } elseif (is_string($module['options']) && SerialValidator::isValid($module['options'])) {
                                    $options = unserialize($module['options']);
                                }


                                // Get default module options
                                $options = (is_array($module['options'])) ? $module['options'] : unserialize($module['options']);

                                // Prepare advanced module options from DB and rewrite defaults
                                if (Yii::$app->getModule('admin/options') && isset(Yii::$app->options)) {
                                    foreach ($options as $option => $value) {
                                        if (Yii::$app->options->get($module['module'] . '.' . $option))
                                            $options[$option] = Yii::$app->options->get($module['module'] . '.' . $option);
                                    }
                                }

                                // Setup the module as a child module of `admin'
                                if ($admin = $app->getModule('admin')) {
                                    $admin->setModule($module['module'], ArrayHelper::merge([
                                        'class' => $module['class']
                                    ], $options));
                                }

                                // Register and load child module
                                if ($installed = $app->getModule('admin/' . $module['module'], true)) {

                                    // Register the translation for the loadable module,
                                    // if it has not been registered before (possibly not inherited from BaseModule)
                                    if (!($app instanceof \yii\console\Application) && $this->_module) {
                                        if (!isset(Yii::$app->getI18n()->translations['app/modules/' . $installed->id])) {
                                            if ($translations = Yii::$app->getModule('admin/translations')) {
                                                $translations->registerTranslations($installed, true);
                                            } else {
                                                $this->_module->registerTranslations($installed);
                                            }
                                        }
                                    }

                                    // Configure dashboard layout
                                    $installed->layout = 'dashboard';
                                    $installed->layoutPath = '@wdmg/admin/views/layouts';

                                    // Configure migrations lookup
                                    $migrationLookup[] = $installed->getBaseAlias() . '/migrations';

                                    // Configure base route prefix
                                    if (isset($module->routePrefix))
                                        $installed->routePrefix = $module->routePrefix;

                                    // Check instance of BootstrapInterface
                                    if ($installed instanceof yii\base\BootstrapInterface) {
                                        $installed->bootstrap(Yii::$app);
                                    } else if ($module['bootstrap']) {
                                        Yii::$app->bootstrap[] = $module['bootstrap'];
                                    } else {
                                        throw new InvalidConfigException('Module `' . $module['class'] . '` must implement BootstrapInterface interface');
                                    }

                                }
                            }
                        }
                    }
                }
            } else {
                Yii::debug('No child modules available for loading', __METHOD__);
            }
        }

        // Set error handler
        if (!($app instanceof \yii\console\Application) && $this->isBackend()) {
            if ($errorHandler = $app->getErrorHandler()) {

                if (Yii::$app->getModule('admin/rbac'))
                    $errorHandler->errorAction = 'admin/rbac/rbac/error';
                else
                    $errorHandler->errorAction = 'admin/error';

            } else {
                $app->setComponents([
                    'errorHandler' => [
                        'errorAction' => (Yii::$app->getModule('admin/rbac')) ?
                            'admin/rbac/rbac/error' :
                            'admin/error'
                    ]
                ]);
            }
        }

        // For console only
        if ($app instanceof \yii\console\Application) {

            $migrationLookup = [];
            $support = $this->_module->getSupportModules();

            // Polyfill for array_key_first() for PHP <= 7.3.0
            if (!function_exists('array_key_first')) {
                function array_key_first(array $arr) {
                    foreach($arr as $key => $unused) {
                        return $key;
                    }
                    return NULL;
                }
            }

            // Configure migrations for all modules
            foreach ($app->extensions as $key => $extension) {
                // Limit the output of only those modules that are supported by the system.
                if (in_array($extension['name'], $support)) {

                    $alias = array_key_first($extension['alias']);

                    //$migrationLookup[] = BaseFileHelper::normalizePath(Yii::getAlias($alias) . '/migrations');
                    $migrationLookup[] = BaseFileHelper::normalizePath($extension['alias'][$alias] . '/migrations');
                }
            }

            $app->controllerMap['migrate'] = [
                'class' => 'wdmg\admin\commands\MigrateController',
                'migrationLookup' => $migrationLookup
            ];

            // Configure urlManager
            /*if ($app->getModule('admin/options') && isset(Yii::$app->options)) {
                if ($hostInfo = Yii::$app->options->get('urlManager.hostInfo')) {
                    $app->getUrlManager()->setHostInfo($hostInfo);
                    $_SERVER['SERVER_NAME'] = $hostInfo;
                }

                if ($baseUrl = Yii::$app->options->get('urlManager.baseUrl')) {
                    $app->getUrlManager()->setBaseUrl($baseUrl);
                    $_SERVER['HTTP_HOST'] = $baseUrl;
                }
            }*/ // @TODO: Need review, see /yiisoft/yii2/web/UrlManager.php:640
        }
    }
}