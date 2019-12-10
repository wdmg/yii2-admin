<?php

namespace wdmg\admin;

/**
 * @author          Alexsander Vyshnyvetskyy <alex.vyshnyvetskyy@gmail.com>
 * @copyright       Copyright (c) 2019 W.D.M.Group, Ukraine
 * @license         https://opensource.org/licenses/MIT Massachusetts Institute of Technology (MIT) License
 */

use wdmg\base\BaseModule;
use yii\base\BootstrapInterface;
use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use yii\helpers\BaseFileHelper;


class Bootstrap implements BootstrapInterface
{

    public $module;

    public function bootstrap($app)
    {
        // Get the module instance
        $this->module = Yii::$app->getModule('admin');

        // Get translations module
        $translations = Yii::$app->getModule('admin/translations');

        // Add module URL rules
        $app->getUrlManager()->enablePrettyUrl = true;
        $app->getUrlManager()->showScriptName = false;
        $app->getUrlManager()->enableStrictParsing = true;
        $app->getUrlManager()->addRules(
            [
                'admin' => 'admin/admin/index',
                'admin/<action:(index|modules|login|logout|restore|checkpoint|bugreport)>' => 'admin/admin/<action>',
                [
                    'pattern' => 'admin/index',
                    'route' => 'admin/admin/index',
                    'suffix' => '',
                ], [
                    'pattern' => 'admin/<action:(index|modules|login|logout|restore|checkpoint|bugreport)>',
                    'route' => 'admin/admin/<action>',
                    'suffix' => '',
                ],
                '<module:admin>/<controller:\w+>' => '<module>/<controller>',
                '<module:admin>/<submodule:\w+>/<controller:\w+>' => '<module>/<submodule>/<controller>',
                '<module:admin>/<controller:\w+>/<action:\w+>' => '<module>/<controller>/<action>',
                '<module:admin>/<submodule:\w+>/<controller:\w+>/<action:\w+>' => '<module>/<submodule>/<controller>/<action>',
                [
                    'pattern' => '<module:admin>/',
                    'route' => '<module>/admin/index',
                    'suffix' => '',
                ], [
                    'pattern' => '<module:admin>/<controller:\w+>/',
                    'route' => '<module>/<controller>',
                    'suffix' => '',
                ], [
                    'pattern' => '<module:admin>/<controller:\w+>/<action:\w+>',
                    'route' => '<module>/<controller>/<action>',
                    'suffix' => '',
                ],
            ],
            true
        );

        // Register language of user interface
        if (!($app instanceof \yii\console\Application)) {
            $lang = $app->session->get('language', false);
            if ($app->request->get('lang', false)) {
                $lang = $app->request->get('lang');
                $app->session->set('language', $lang);
                $app->language = $lang;
            } else if (isset($lang)) {
                $lang = $app->session->get('language');
                $app->language = $lang;
            }
        }

        // Configure languages menu for UI
        if (!($app instanceof \yii\console\Application) && $this->module) {
            \yii\base\Event::on(\yii\base\Controller::class, \yii\base\Controller::EVENT_BEFORE_ACTION, function ($event) use ($translations) {

                $langs = [];
                $locales = $this->module->getSupportLanguages();
                if ($translations) {

                    // Register translations for admin module
                    $translations->registerTranslations($this->module, true);

                    $bundle = \wdmg\translations\FlagsAsset::register(Yii::$app->view);
                    foreach ($locales as $locale => $name) {

                        $locale = $translations->parseLocale($locale, Yii::$app->language);
                        if (!($country = $locale['domain']))
                            $country = '_unknown';

                        $flag = \yii\helpers\Html::img($bundle->baseUrl . '/flags-iso/flat/24/'.$country.'.png');

                        $langs[] = [
                            'label' => $flag . '&nbsp;' . $locale['name'],
                            'url' => '?lang='.$locale['locale'],
                            'active'=> (Yii::$app->language == $locale['locale']) ? true : false,
                            'options' => [
                                'class' => (Yii::$app->language == $locale['locale']) ? 'active' : false
                            ]
                        ];
                    }
                } else {

                    // Register translations for admin module
                    $this->module->registerTranslations($this->module);

                    foreach ($locales as $locale => $name) {
                        $langs[] = [
                            'label' => $name,
                            'url' => '?lang='.$locale,
                            'active'=> (Yii::$app->language == $locale) ? true : false,
                            'options' => [
                                'class' => (Yii::$app->language == $locale) ? 'active' : false
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
        if(Yii::$app->db->schema->getTableSchema(\wdmg\admin\models\Modules::tableName())) {
            $migrationLookup = [];
            $model = new \wdmg\admin\models\Modules();
            $modules = $model::getModules(true);
            if (is_array($modules)) {
                
                foreach ($modules as $module) {

                    if (!class_exists($module['class'])) {
                        throw new InvalidConfigException('Can\'t load module `' . $module['class'] . '`');
                    } else {

                        // Check if this module has been loaded before
                        if (!$app->hasModule('admin/' . $module['module'])) {

                            // Get default module options
                            $options = (is_array($module['options'])) ? $module['options'] : unserialize($module['options']);

                            // Prepare advanced module options from DB
                            if (Yii::$app->getModule('admin/options') && isset(Yii::$app->options)) {
                                foreach ($options as $option => $value) {
                                    if (Yii::$app->options->get($module['module'] . '.' . $option))
                                        $options[$option] = Yii::$app->options->get($module['module'] . '.' . $option);
                                }
                            }

                            // Register the module as a child module of `admin'
                            $app->getModule('admin')->setModule($module['module'], ArrayHelper::merge([
                                'class' => $module['class']
                            ], $options));

                            // Check if the module is registered
                            $installed = $app->getModule('admin/' . $module['module']);
                            if ($installed) {

                                // Register translations for loading module
                                if (!($app instanceof \yii\console\Application) && $this->module) {
                                    if ($translations = Yii::$app->getModule('admin/translations'))
                                        $translations->registerTranslations($installed, true);
                                    else
                                        $this->module->registerTranslations($installed);
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
        }

        // Configure migrations for all modules
        if (Yii::$app instanceof \yii\console\Application) {

            $migrationLookup = [];
            $support = $this->module->getSupportModules();

            foreach (Yii::$app->extensions as $key => $extension) {
                // Limit the output of only those modules that are supported by the system.
                if (in_array($extension['name'], $support)) {
                    $alias = array_key_first($extension['alias']);
                    //$migrationLookup[] = BaseFileHelper::normalizePath(Yii::getAlias($alias) . '/migrations');
                    $migrationLookup[] = BaseFileHelper::normalizePath($extension['alias'][$alias] . '/migrations');
                }
            }

            Yii::$app->controllerMap['migrate'] = [
                'class' => 'wdmg\admin\commands\MigrateController',
                'migrationLookup' => $migrationLookup
            ];
        }
    }
}