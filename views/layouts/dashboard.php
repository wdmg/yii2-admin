<?php

/* @var $this \yii\web\View */
/* @var $content string */

use app\widgets\Alert;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use yii\widgets\Pjax;
use app\assets\AppAsset;

AppAsset::register($this);

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
<body>
<?php $this->beginBody() ?>
<div class="wrap">
<?php
    NavBar::begin([
        'brandLabel' => "Butterfly.CMS",
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
            Yii::$app->user->isGuest ? (
            ['label' => 'Login', 'url' => ['/admin/login']]
            ) : (
                '<li>'
                . Html::beginForm(['/admin/logout'], 'post')
                . Html::submitButton(
                    'Logout (' . Yii::$app->user->identity->username . ')',
                    ['class' => 'btn btn-link logout']
                )
                . Html::endForm()
                . '</li>'
            )
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
                    'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                ]) ?>
                <?= Alert::widget() ?>
                <?= $content ?>
                <?php Pjax::end(); ?>
            </div>
        </div>
    </div>
</div>
<footer class="footer">
    <div class="container-fluid">
        <p class="pull-left">&copy; <?= date('Y') ?>, Butterfly.CMS</p>

        <p class="pull-right"><?= Yii::powered() ?></p>
    </div>
</footer>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
