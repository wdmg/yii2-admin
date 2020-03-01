<?php


namespace wdmg\admin;

/**
 * @author          Alexsander Vyshnyvetskyy <alex.vyshnyvetskyy@gmail.com>
 * @copyright       Copyright (c) 2019 W.D.M.Group, Ukraine
 * @license         https://opensource.org/licenses/MIT Massachusetts Institute of Technology (MIT) License
 */

use yii\web\AssetBundle;

class FontAwesomeAssets extends AssetBundle
{

    public $sourcePath = '@bower/font-awesome';

    public function init()
    {
        parent::init();
        $this->css = YII_DEBUG ? ['css/all.css'] : ['css/all.min.css'];
        $this->depends = [\yii\web\JqueryAsset::class];
    }

}