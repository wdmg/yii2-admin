<?php

namespace wdmg\admin\components;


/**
 * Yii2 Dashboard
 *
 * @category        Component
 * @version         1.3.0
 * @author          Alexsander Vyshnyvetskyy <alex.vyshnyvetskyy@gmail.com>
 * @link            https://github.com/wdmg/yii2-admin
 * @copyright       Copyright (c) 2019 - 2021 W.D.M.Group, Ukraine
 * @license         https://opensource.org/licenses/MIT Massachusetts Institute of Technology (MIT) License
 *
 */

use Yii;
use yii\base\Component;
use yii\helpers\Url;
use wdmg\helpers\ArrayHelper;
use wdmg\search\models\LiveSearch;

class Dashboard extends Component
{

    protected $module;
    protected $model;

    public $search = null;

    /**
     * Initialize the component
     *
     * @throws \yii\base\InvalidConfigException
     */
    public function init()
    {
        $this->module = Yii::$app->getModule('admin');

        if ($search = $this->module->getModule('search')) {

            if (isset($search->supportModels['news'])) {
                $search->supportModels['news']['options']['conditions'] = [];
                $search->supportModels['news']['options']['url'] = function ($model) {
                    return [
                        'view' => Url::toRoute(['news/news/view', 'id' => $model->id]),
                        'update' => Url::toRoute(['news/news/update', 'id' => $model->id]),
                        'public' => $model->url,
                    ];
                };
            }

            if (isset($search->supportModels['blog'])) {
                $search->supportModels['blog']['options']['conditions'] = [];
                $search->supportModels['blog']['options']['url'] = function ($model) {
                    return [
                        'view' => Url::toRoute(['blog/posts/view', 'id' => $model->id]),
                        'update' => Url::toRoute(['blog/posts/update', 'id' => $model->id]),
                        'public' => $model->url,
                    ];
                };
            }

            if (isset($search->supportModels['pages'])) {
                $search->supportModels['pages']['options']['conditions'] = [];
                $search->supportModels['pages']['options']['url'] = function ($model) {
                    return [
                        'view' => "/admin/pages/pages/view/?id=" . $model->id,
                        'update' => "/admin/pages/pages/update/?id=" . $model->id,
                        'public' => $model->url,
                    ];
                };
            }

            $this->search = new LiveSearch();
        }

        parent::init();
    }

    /**
     * Sorts menu items by array value.
     * Use as callback-function for uasort().
     *
     * @param $a array
     * @param $b array
     * @return integer
     * @see uasort()
     */
    function sortByOrder($a, $b) {

        if (isset($a['order']) && isset($b['order'])) {
            if ($a['order'] === $b['order']) return 0;
            return $a['order'] < $b['order'] ? -1 : 1;
        } else {
            return null;
        }
    }

    /**
     * Generate administrative interface menu items
     *
     * @return array of menu items
     */
    public function getSidebarMenuItems()
    {
        $items = [];
        $model = new \wdmg\admin\models\Modules();
        $modules = $model::getModules(true);
        $menuItems = $this->module->getMenuItems();
        uasort($menuItems, array($this, 'sortByOrder'));

        foreach ($menuItems as $menu) {

            $subitems = [];
            $navitems = [];
            $disabled = false;

            // First, check if the menu item points to a specific module
            if (isset($menu['item'])) {

                // Check the presence of the module identifier among the available packages
                foreach ($modules as $module) {
                    if ($menu['item'] == $module['module']) {
                        if($module = Yii::$app->getModule('admin/'. $module['module'], false)) {

                            // Call Module::dashboardNavItems() to get its native menu
                            $navitems = [];
                            $moduleNavitems = $module->dashboardNavItems();
                            if (ArrayHelper::isIndexed($moduleNavitems) && !ArrayHelper::isAssociative($moduleNavitems, true)) {
                                foreach ($moduleNavitems as $moduleNavitem) {
                                    $navitems[] = $moduleNavitem;
                                }
                            } else  {
                                $navitems = $moduleNavitems;
                            }

                            // Check if the received menu item contains a direct link
                            if (isset($navitems['url']))
                                $menu['url'] = $navitems['url'];

                            // Check if the received menu item contains sub-items
                            if ($navitems['items']) {
                                $menu['items'] = $navitems['items'];
                            }

                            unset($navitems);
                        }
                    }
                }
            }

            // Check if the menu item has nested sub-items
            if (isset($menu['items']) && is_array($menu['items'])) {

                // If the nested item is not represented by an array, then this is the module identifier,
                // of the module in which you need to call Module::dashboardNavItems() to get its native menu
                if (!is_array($menu['items'][0])) {
                    $found = 0;
                    foreach ($menu['items'] as $moduleId) {

                        // add custom link for dashboard page
                        if (is_array($moduleId)) {
                            if (
                                array_key_exists('label', $moduleId) &&
                                array_key_exists('icon', $moduleId) &&
                                array_key_exists('url', $moduleId)
                            ) {
                                $navitems[] = $moduleId;
                                $found++;
                            }
                        } else {
                            // check the presence of the module identifier among the available packages
                            foreach ($modules as $module) {
                                if ($moduleId == $module['module']) {
                                    if ($module = Yii::$app->getModule('admin/'. $module['module'])) {
                                        $moduleNavitems = $module->dashboardNavItems();
                                        if (ArrayHelper::isIndexed($moduleNavitems) && !ArrayHelper::isAssociative($moduleNavitems, true)) {
                                            foreach ($moduleNavitems as $moduleNavitem) {
                                                $navitems[] = $moduleNavitem;
                                            }
                                        } else  {
                                            $navitems[] = $moduleNavitems;
                                        }
                                        $found++;
                                    }
                                }
                            }
                        }
                    }

                    // None of the modules were found
                    if ($found == 0) {
                        $disabled = true;
                    } else {
                        foreach ($navitems as $navitem) {
                            if ($navitem['icon'])
                                $navitem['label'] = ($navitem['icon']) ? '<span class="icon"><i class="' . $navitem['icon'] . '"></i></span> ' . Yii::t('app/modules/admin', $navitem['label']) : Yii::t('app/modules/admin', $navitem['label']);
                        }
                    }

                } else {

                    // It means a nested array and it already contains submenus of the menu
                    $submenus = $menu['items'];
                    uasort($submenus, array($this, 'sortByOrder'));
                    foreach ($submenus as $submenu) {
                        
                        $navitems = [];
                        if (isset($submenu['items']) && is_array($submenu['items'])) {
                            foreach ($submenu['items'] as $moduleId) {

                                // check the presence of the module identifier among the available packages
                                foreach ($modules as $module) {
                                    if ($moduleId == $module['module']) {
                                        if ($module = Yii::$app->getModule('admin/'. $module['module'])) {
                                            $moduleNavitems = $module->dashboardNavItems();
                                            if (ArrayHelper::isIndexed($moduleNavitems) && !ArrayHelper::isAssociative($moduleNavitems, true)) {
                                                foreach ($moduleNavitems as $moduleNavitem) {
                                                    $navitems[] = $moduleNavitem;
                                                }
                                            } else  {
                                                $navitems[] = $moduleNavitems;
                                            }
                                        }
                                    }
                                }
                            }
                        }

                        // Collect the final sub-menu item
                        $subitems[] = [
                            'label' => ($submenu['icon']) ? '<span class="icon"><i class="' . $submenu['icon'] . '"></i></span> ' . Yii::t('app/modules/admin', $submenu['label']) : Yii::t('app/modules/admin', $submenu['label']),
                            'url' => ($submenu['url']) ? Url::to($submenu['url']) : '#',
                            'items' => ($navitems) ? $navitems : false
                        ];
                        unset($navitems);
                    }
                }
            } else {
                if (!isset($menu['url']) && !isset($menu['item']))
                    $disabled = true;
            }

            // Check if the icon is installed for this menu item
            if (isset($navitems)) {
                if (count($navitems) > 0) {
                    foreach ($navitems as $nav => $item) {
                        if ($item['icon']) {
                            $navitems[$nav]['label'] = ($item['icon']) ? '<span class="icon"><i class="' . $item['icon'] . '"></i></span> ' . Yii::t('app/modules/admin', $item['label']) : Yii::t('app/modules/admin', $item['label']);
                        }
                    }
                }
            }

            // Collect the final parent menu item
            $items[] = [
                'label' => ($menu['icon']) ? '<span class="icon"><i class="' . $menu['icon'] . '"></i></span> ' . Yii::t('app/modules/admin', $menu['label']) : Yii::t('app/modules/admin', $menu['label']),
                'url' => isset($menu['url']) ? Url::to($menu['url']) : '#',
                'items' => ($subitems) ? $subitems : (($navitems) ? $navitems : false),
                'active' => false,
                'options' => ['class' => ($disabled) ? 'disabled' : ''],
            ];
        }

        return $items;
    }


    /**
     * Generate create menu items for administrative interface
     *
     * @return array of menu items
     */
    public function getCreateMenuItems() {
        $items = [];
        $model = new \wdmg\admin\models\Modules();
        $modules = $model::getModules(true);
        $menuItems = $this->module->getCreateMenuItems();

        foreach ($menuItems as $moduleId => $menu) {
            foreach ($modules as $module) {

                // Check the presence of the module identifier among the available packages
                if ($moduleId == $module['name']) {
                    if ($module = Yii::$app->getModule('admin/'. $module['module'], false)) {

                        // Add items for create menu in Dashboard
                        if (isset($menu['label']) && isset($menu['url'])) {
                            $items[] = [
                                'label' => Yii::t('app/modules/admin', $menu['label']),
                                'url' => $menu['url']
                            ];
                        } else if (is_array($menu)) {
                            foreach ($menu as $item) {
                                if (isset($item['label']) && isset($item['url'])) {
                                    $items[] = [
                                        'label' => Yii::t('app/modules/admin', $item['label']),
                                        'url' => $item['url']
                                    ];
                                }
                            }
                        }
                    }
                }
            }
        }

        return $items;
    }

    /**
     * Return app version
     *
     * @return mixed
     */
    public function getAppVersion()
    {
        if (in_array(Yii::$app->id, ['butterfly-cms', 'butterfly-cms-console', 'butterfly-cms-tests']))
            return 'v'.Yii::$app->getVersion();
        else
            return false;
    }
}

?>