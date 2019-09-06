<?php

/* @var $this \yii\web\View */
/* @var $content string */

use app\widgets\Alert;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use yii\widgets\Pjax;
use yii\bootstrap\Dropdown;
use yii\bootstrap\ButtonDropdown;
//use app\assets\AppAsset;
use wdmg\admin\AdminAsset;

//AppAsset::register($this);
$bundle = AdminAsset::register($this);
$this->registerLinkTag(['rel' => 'shortcut icon', 'type' => 'image/x-icon', 'href' => Url::to($bundle->baseUrl . '/favicon.ico')]);
$this->registerLinkTag(['rel' => 'icon', 'type' => 'image/png', 'href' => Url::to($bundle->baseUrl . '/favicon.png')]);

?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body class="welcome <?= (YII_ENV_DEV) ? 'env-dev' : '' ?>">
    <?php $this->beginBody() ?>
    <div class="container-fluid">
        <div class="row">
            <?php Pjax::begin([
                'id' => 'authAjax',
                'timeout' => 10000
            ]); ?>
            <?php
            $label = 'Language';
            foreach ($this->params['langs'] as $lang) {
                if ($lang['active'] === true) {
                    $label = $lang['label'];
                    break;
                }
            }
            echo ButtonDropdown::widget([
                'label' => $label,
                'containerOptions' => [
                    'id' => 'languageSelector',
                    'class' => 'lang-select'
                ],
                'dropdown' => [
                    'options' => [
                        'class' => 'dropdown-menu-right'
                    ],
                    'items' => $this->params['langs'],
                ],
            ]); ?>
            <?= Alert::widget() ?>
            <?= $content ?>
            <?php Pjax::end(); ?>
        </div>
    </div>
    <footer class="footer">
        <div class="container-fluid">
            <div class="row">
                <div class="col-xs-12 col-md-6 text-left">
                    <p>
                        &copy; <?= date('Y') ?>, <?= Html::a('Butterfly.CMS', 'https://butterflycms.com/', ['target' => "_blank"]) ?>
                        <?= Yii::$app->dashboard->getAppVersion(); ?>
                    </p>
                </div>
                <div class="col-xs-12 col-md-6 text-right">
                    <p>Created by <?= Html::a('W.D.M.Group, Ukraine', 'https://wdmg.com.ua/', ['target' => "_blank"]) ?></p>
                </div>
            </div>
        </div>
    </footer>
    <?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>