<?php

namespace wdmg\admin\components;


/**
 * Yii2 Dashboard
 *
 * @category        Component
 * @version         1.0.10
 * @author          Alexsander Vyshnyvetskyy <alex.vyshnyvetskyy@gmail.com>
 * @link            https://github.com/wdmg/yii2-admin
 * @copyright       Copyright (c) 2019 W.D.M.Group, Ukraine
 * @license         https://opensource.org/licenses/MIT Massachusetts Institute of Technology (MIT) License
 *
 */

use Yii;
use yii\base\Component;

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
        if ($a['order'] === $b['order']) return 0;
        return $a['order'] < $b['order'] ? -1 : 1;
    }

    /**
     * Generate administrative interface menu items
     *
     * @return array of menu items
     */
    public function getSidebarMenuItems()
    {
        $items = [];
        $menuItems = $this->module->getMenuItems();
        uasort($menuItems, array($this, 'sortByOrder'));
        foreach ($menuItems as $menu) {

            $subitems = [];
            $navitems = [];
            $disabled = false;

            // first, check if the menu item points to a specific module
            if (isset($menu['item'])) {

                // check the presence of the module identifier among the available packages
                foreach ($this->module->packages as $package) {
                    if ($menu['item'] == $package['moduleId']) {
                        if($module = Yii::$app->getModule('admin/'. $package['moduleId'])) {

                            // call Module::dashboardNavItems() to get its native menu
                            $navitems = $module->dashboardNavItems();

                            // check if the received menu item contains a direct link
                            if (isset($navitems['url']))
                                $menu['url'] = $navitems['url'];

                            // check if the received menu item contains sub-items
                            if ($navitems['items']) {
                                $menu['items'] = $navitems['items'];
                            }

                            unset($navitems);
                        }
                    }
                }
            }

            // check if the menu item has nested sub-items
            if (isset($menu['items']) && is_array($menu['items'])) {
                // if the nested item is not represented by an array, then this is the module identifier,
                // of the module in which you need to call Module::dashboardNavItems() to get its native menu
                if (!is_array($menu['items'][0])) {
                    $found = 0;
                    foreach ($menu['items'] as $moduleId) {
                        // check the presence of the module identifier among the available packages
                        foreach ($this->module->packages as $package) {
                            if ($moduleId == $package['moduleId']) {
                                if($module = Yii::$app->getModule('admin/'. $package['moduleId'])) {
                                    $navitems[] = $module->dashboardNavItems();
                                    $found++;
                                }
                            }
                        }
                    }

                    // none of the modules were found
                    if ($found == 0)
                        $disabled = true;

                } else {
                    // it means a nested array and it already contains submenus of the menu
                    $submenus = $menu['items'];
                    uasort($submenus, array($this, 'sortByOrder'));
                    foreach ($submenus as $submenu) {

                        $navitems = [];
                        if (isset($submenu['items']) && is_array($submenu['items'])) {
                            foreach ($submenu['items'] as $moduleId) {

                                // check the presence of the module identifier among the available packages
                                foreach ($this->module->packages as $package) {
                                    if ($moduleId == $package['moduleId']) {
                                        if($module = Yii::$app->getModule('admin/'. $package['moduleId']))
                                            $navitems[] = $module->dashboardNavItems();
                                    }
                                }
                            }
                        }

                        // collect the final sub-menu item
                        $subitems[] = [
                            'label' => ($submenu['icon']) ? '<span class="fa-stack fa-lg"><i class="fa ' . $submenu['icon'] . ' fa-stack-1x"></i></span> ' . Yii::t('app/modules/admin', $submenu['label']) : Yii::t('app/modules/admin', $submenu['label']),
                            'url' => ($submenu['url']) ? \yii\helpers\Url::to($submenu['url']) : '#',
                            'items' => ($navitems) ? $navitems : false,
                        ];
                        unset($navitems);
                    }
                }
            } else {
                if (!isset($menu['url']) && !isset($menu['item']))
                    $disabled = true;
            }

            // collect the final parent menu item
            $items[] = [
                'label' => ($menu['icon']) ? '<span class="fa-stack fa-lg"><i class="fa ' . $menu['icon'] . ' fa-stack-1x"></i></span> ' . Yii::t('app/modules/admin', $menu['label']) : Yii::t('app/modules/admin', $menu['label']),
                'url' => ($menu['url']) ? \yii\helpers\Url::to($menu['url']) : '#',
                'items' => ($subitems) ? $subitems : $navitems,
                'active' => false,
                'options' => ['class' => ($disabled) ? 'disabled' : '']
            ];
        }

        return $items;
    }
}

?>