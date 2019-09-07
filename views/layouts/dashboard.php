<?php

/* @var $this \yii\web\View */
/* @var $content string */

use app\widgets\Alert;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\bootstrap\Progress;
use yii\widgets\Breadcrumbs;
use yii\widgets\Pjax;
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
<body class="dashboard <?= (YII_ENV_DEV) ? 'env-dev' : '' ?>">
    <?php $this->beginBody() ?>
    <div class="<?= 'module-' . $this->context->module->id; ?> <?= 'action-' . $this->context->action->id; ?>">
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

        $items = [];
        if (Yii::$app->getModule('admin/terminal', false))
            $items[] = [
                'label' => '<span class="fa fa-fw fa-terminal"></span> Terminal',
                'url' => '#terminal'
            ];

        if (Yii::$app->getModule('admin/translations', false))
            $items[] = [
                'label' => '<span class="fa fa-fw fa-language"></span> Language',
                'items' => $this->params['langs']
            ];
        else
            $items[] = [
                'label' => '<span class="fa fa-fw fa-language"></span> Language',
                'items' => $this->params['langs']
            ];

        if (Yii::$app->user->isGuest)
            $items[] = [
                'label' => '<span class="fa fa-fw fa-sign-in"></span> Login',
                'url' => ['/admin/login']
            ];
        else
            $items[] = [
                'label' => '<span class="fa fa-fw fa-user-o"></span> Logout (' . Yii::$app->user->identity->username . ')',
                'url' => ['/admin/logout'], 'linkOptions' => ['data-method' => 'post']
            ];


        echo Nav::widget([
            'options' => ['class' => 'navbar-nav navbar-right'],
            'items' => $items,
            'encodeLabels' => false
        ]);
        NavBar::end();
        echo Progress::widget([
            'id' => 'requestProgress',
            'percent' => 0,
            'options' => ['style' => 'display: none;'],
            'barOptions' => ['class' => 'progress-bar-info']
        ]);
    ?>
        <div class="container-fluid">
            <div class="row" style="padding-top:72px;">
                <div class="col-xs-12 col-sm-3 col-md-2 sidebar">
                    <?= Nav::widget([
                        'id' => 'sidebarNav',
                        'options' => ['class' => 'nav nav-sidebar'],
                        'items' => Yii::$app->dashboard->getSidebarMenuItems(),
                        'activateParents' => true,
                        'dropDownCaret' => '<span class="fa fa-fw fa-angle-down"></span>',
                        'encodeLabels' => false
                    ]); ?>
                </div>
                <div class="col-xs-12 col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
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
        '$(document).ready(function() {
        
            setInterval(function() {
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
            }, 5000);
            
        });'
    ); ?>

    <?php $this->registerJs(<<< JS
        $('body').delegate('a[href="#terminal"]', 'click', function(event) {
            event.preventDefault();
            $.get(
                '/admin/terminal',
                function (data) {
                    $('#terminalModal .modal-body').html($(data).remove('.modal-footer'));
                    if ($(data).find('.modal-footer').length > 0) {
                        $('#terminalModal').find('.modal-footer').remove();
                        $('#terminalModal .modal-content').append($(data).find('.modal-footer'));
                    }
                    $('#terminalModal').modal();
                }
            );
        });
JS
    ); ?>
    <?php yii\bootstrap\Modal::begin([
        'id' => 'terminalModal',
        'header' => '<h4 class="modal-title">'.Yii::t('app/modules/admin', 'Terminal').'</h4>',
    ]); ?>
    <?php yii\bootstrap\Modal::end(); ?>

    <footer class="footer">
        <div class="container-fluid">
            <div class="row">
                <div class="col-xs-12 col-md-4 text-left">
                    <p>
                        &copy; <?= date('Y') ?>, <?= Html::a('Butterfly.CMS', 'https://butterflycms.com/', ['target' => "_blank"]) ?>
                        <?= Yii::$app->dashboard->getAppVersion(); ?>
                    </p>
                </div>
                <div class="col-xs-12 col-md-4 text-center">
                    <p>
                        <?= Html::a(
                            Html::tag('span', '', ['class' => 'fa fa-fw fa-bug']) .
                            Yii::t('app/modules/admin', 'Report a bug'),
                            '#bugreport',
                            ['class' => 'text-danger']
                        ) ?>

                    </p>
                </div>
                <div class="col-xs-12 col-md-4 text-right">
                    <p>Created by <?= Html::a('W.D.M.Group, Ukraine', 'https://wdmg.com.ua/', ['target' => "_blank"]) ?></p>
                </div>
            </div>
        </div>
    </footer>

    <?php $this->registerJs(<<< JS
        $('body').delegate('a[href="#bugreport"]', 'click', function(event) {
            event.preventDefault();
            $.get(
                '/admin/bugreport',
                function (data) {
                    $('#bugreportModal .modal-body').html($(data).remove('.modal-footer'));
                    if ($(data).find('.modal-footer').length > 0) {
                        $('#bugreportModal').find('.modal-footer').remove();
                        $('#bugreportModal .modal-content').append($(data).find('.modal-footer'));
                    }
                    $('#bugreportModal').modal();
                }
            );
        });
JS
    ); ?>
    <?php yii\bootstrap\Modal::begin([
        'id' => 'bugreportModal',
        'header' => '<h4 class="modal-title">'.Yii::t('app/modules/admin', 'Bug Report').'</h4>',
    ]); ?>
    <?php yii\bootstrap\Modal::end(); ?>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
