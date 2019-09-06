<?php

namespace wdmg\admin\components;


/**
 * Yii2 Dashboard
 *
 * @category        Component
 * @version         1.1.7
 * @author          Alexsander Vyshnyvetskyy <alex.vyshnyvetskyy@gmail.com>
 * @link            https://github.com/wdmg/yii2-admin
 * @copyright       Copyright (c) 2019 W.D.M.Group, Ukraine
 * @license         https://opensource.org/licenses/MIT Massachusetts Institute of Technology (MIT) License
 *
 */

use Yii;
use yii\base\Component;
use yii\helpers\ArrayHelper;

class Dashboard extends Component
{

    protected $module;
    protected $model;

    /**
     * Initialize the component
     *
     * @throws \yii\base\InvalidConfigException
     */
    public function init()
    {
        $this->module = Yii::$app->getModule('admin');
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
                        if($module = Yii::$app->getModule('admin/'. $module['module'])) {

                            // Call Module::dashboardNavItems() to get its native menu
                            $navitems = $module->dashboardNavItems();

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

                        // check the presence of the module identifier among the available packages
                        foreach ($modules as $module) {
                            if ($moduleId == $module['module']) {
                                if($module = Yii::$app->getModule('admin/'. $module['module'])) {
                                    $navitems[] = $module->dashboardNavItems();
                                    $found++;
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
                                $navitem['label'] = ($navitem['icon']) ? '<span class="fa-stack"><i class="fa ' . $navitem['icon'] . ' fa-stack-1x"></i></span> ' . Yii::t('app/modules/admin', $navitem['label']) : Yii::t('app/modules/admin', $navitem['label']);
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
                                        if($module = Yii::$app->getModule('admin/'. $module['module']))
                                            $navitems[] = $module->dashboardNavItems();
                                    }
                                }
                            }
                        }

                        // Collect the final sub-menu item
                        $subitems[] = [
                            'label' => ($submenu['icon']) ? '<span class="fa-stack"><i class="fa ' . $submenu['icon'] . ' fa-stack-1x"></i></span> ' . Yii::t('app/modules/admin', $submenu['label']) : Yii::t('app/modules/admin', $submenu['label']),
                            'url' => ($submenu['url']) ? \yii\helpers\Url::to($submenu['url']) : '#',
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
                            $navitems[$nav]['label'] = ($item['icon']) ? '<span class="fa-stack"><i class="fa ' . $item['icon'] . ' fa-stack-1x"></i></span> ' . Yii::t('app/modules/admin', $item['label']) : Yii::t('app/modules/admin', $item['label']);
                        }
                    }
                }
            }

            // Collect the final parent menu item
            $items[] = [
                'label' => ($menu['icon']) ? '<span class="fa-stack fa-lg"><i class="fa ' . $menu['icon'] . ' fa-stack-1x"></i></span> ' . Yii::t('app/modules/admin', $menu['label']) : Yii::t('app/modules/admin', $menu['label']),
                'url' => isset($menu['url']) ? \yii\helpers\Url::to($menu['url']) : '#',
                'items' => ($subitems) ? $subitems : (($navitems) ? $navitems : false),
                'active' => false,
                'options' => ['class' => ($disabled) ? 'disabled' : ''],
            ];
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