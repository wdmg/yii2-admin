<?php

/* @var $this \yii\web\View */
/* @var $content string */

use app\widgets\Alert;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use yii\widgets\Pjax;
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
    <title><?= Html::encode($this->title . ' — Butterfly.CMS') ?></title>
    <?php $this->head() ?>
</head>
<body class="dashboard">
    <?php $this->beginBody() ?>
    <div class="admin">
        <?php
        NavBar::begin([
            'brandLabel' => Html::img($bundle->baseUrl . '/images/logotype-inline.svg', [
                'class' => "img-responsive",
                'onerror' => "this.src='" . $bundle->baseUrl . '/images/logotype-inline.png' . "'"
            ]),
            'brandUrl' => ['/admin'],
            'options' => [
                'class' => 'navbar-inverse navbar-fixed-top',
            ],
            'innerContainerOptions' => [
                'class' => 'container-fluid',
            ],
            'containerOptions' => [
                'class' => 'row',
            ]
        ]);
        echo Nav::widget([
            'options' => ['class' => 'navbar-nav navbar-right'],
            'items' => [
                ['label' => 'Language', 'items' => $this->params['langs']],
                (Yii::$app->user->isGuest) ? ['label' => 'Login', 'url' => ['/admin/login']] : ['label' => 'Logout (' . Yii::$app->user->identity->username . ')', 'url' => ['/admin/logout'], 'linkOptions' => ['data-method' => 'post']],
            ],
        ]);
        NavBar::end();

    ?>
        <div class="container-fluid">
            <div class="row" style="padding-top:96px;">
                <div class="col-xs-12 col-md-3 col-lg-2">
                <?= Nav::widget([
                    'options' => ['class' => 'nav nav-pills nav-stacked'],
                    'items' => Yii::$app->dashboard->getSidebarMenuItems()
                ]); ?>
                </div>
                <div class="col-xs-12 col-md-9 col-lg-10">
                    <?php Pjax::begin([
                        'id' => 'dashboardAjax',
                        'timeout' => 10000
                    ]); ?>
                    <?= Breadcrumbs::widget([
                        'homeLink' => [
                            'label' => Yii::t('app/modules/admin', 'Main'),
                            'url' => '/admin/'
                        ],
                        'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                    ]) ?>
                    <?= Alert::widget() ?>
                    <?= $content ?>
                    <?php Pjax::end(); ?>
                </div>
            </div>
        </div>
    </div>
    <?php $this->registerJs(
        'setInterval(function() {
            $.ajax({
                type: \'POST\',
                url: \'/admin/checkpoint\',
                dataType: \'json\',
                complete: function(data) {
                    if(data) {
                        console.log(data.responseJSON.loggedin);
                        if (data.status == 200 && data.responseJSON.loggedin) {
                            return true;
                        }
                    }
                    window.location.href = \'/admin/login\';
                }
            });
        }, 5000);'
    ); ?>
    <footer class="footer">
        <div class="container-fluid">
            <div class="row">
                <div class="col-xs-12 col-md-6 text-left">
                    <p>&copy; <?= date('Y') ?>, Butterfly.CMS</p>
                </div>
                <div class="col-xs-12 col-md-6 text-right">
                    <p>Created by <?= Html::a('W.D.M.Group, Ukraine', 'http://wdmg.com.ua', ['target' => "_blank"]) ?></p>
                </div>
            </div>
        </div>
    </footer>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
