<?php

namespace wdmg\admin\components;


/**
 * Yii2 Dashboard
 *
 * @category        Component
 * @version         1.0.6
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

    public function getSidebarMenuItems()
    {
        $items = [];
        foreach ($this->module->packages as $package) {
            if($module = Yii::$app->getModule('admin/'. $package['moduleId']))
                $items[] = $module->dashboardNavItems();
        }
        return $items;
    }
}

?>