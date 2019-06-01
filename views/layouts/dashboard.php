<?php

/* @var $this \yii\web\View */
/* @var $content string */

use app\widgets\Alert;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
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
        'brandLabel' => Yii::$app->name,
        'brandUrl' => Yii::$app->homeUrl,
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
            ['label' => 'Home', 'url' => ['/site/index']],
            ['label' => 'About', 'url' => ['/site/about']],
            ['label' => 'Contact', 'url' => ['/site/contact']],
            Yii::$app->user->isGuest ? (
            ['label' => 'Login', 'url' => ['/site/login']]
            ) : (
                '<li>'
                . Html::beginForm(['/site/logout'], 'post')
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
                'items' => [
                    /*Yii::$app->getModule('users')->dashboardNavItems(),
                    Yii::$app->getModule('options')->dashboardNavItems(),
                    Yii::$app->getModule('rbac')->dashboardNavItems(),
                    Yii::$app->getModule('geo')->dashboardNavItems(),
                    Yii::$app->getModule('tasks')->dashboardNavItems(),
                    Yii::$app->getModule('tickets')->dashboardNavItems(),
                    Yii::$app->getModule('stats')->dashboardNavItems(),
                    Yii::$app->getModule('forms')->dashboardNavItems(),
                    Yii::$app->getModule('comments')->dashboardNavItems(),
                    Yii::$app->getModule('reviews')->dashboardNavItems(),
                    Yii::$app->getModule('likes')->dashboardNavItems(),
                    Yii::$app->getModule('views')->dashboardNavItems(),
                    Yii::$app->getModule('services')->dashboardNavItems(),*/
                    Yii::$app->getModule('activity')->dashboardNavItems(),
                    /*Yii::$app->getModule('api')->dashboardNavItems(),
                    Yii::$app->getModule('reposts')->dashboardNavItems(),*/
                ],
            ]); ?>
            </div>
            <div class="col-xs-12 col-md-9 col-lg-10">
                <?= Breadcrumbs::widget([
                    'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                ]) ?>
                <?= Alert::widget() ?>
                <?= $content ?>
            </div>
        </div>
    </div>
</div>

<footer class="footer">
    <div class="container">
        <p class="pull-left">&copy; My Company <?= date('Y') ?></p>

        <p class="pull-right"><?= Yii::powered() ?></p>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
