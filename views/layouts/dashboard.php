<?php

/* @var $this \yii\web\View */
/* @var $content string */

use app\widgets\Alert;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\bootstrap\Progress;
use yii\bootstrap\ActiveForm;
use yii\widgets\Breadcrumbs;
use yii\widgets\Pjax;
use wdmg\admin\AdminAsset;
use wdmg\admin\FontAwesomeAssets;

$bundle = AdminAsset::register($this);
$bundle2 = FontAwesomeAssets::register($this);

$this->registerLinkTag(['rel' => 'shortcut icon', 'type' => 'image/x-icon', 'href' => Url::to($bundle->baseUrl . '/favicon.ico')]);
$this->registerLinkTag(['rel' => 'icon', 'type' => 'image/png', 'href' => Url::to($bundle->baseUrl . '/favicon.png')]);
$this->registerJs(<<< JS
    /*$(function () {
        $("[data-toggle='tooltip']").tooltip();
        //$("[data-toggle='modal']").modal();
        $("[data-toggle='popover']").popover(); 
        $('.dropdown-toggle').dropdown();
    });
    $(document).on('pjax:success', function() {
        $("[data-toggle='tooltip']").tooltip();
        //$("[data-toggle='modal']").modal();
        $("[data-toggle='popover']").popover(); 
        $('.dropdown-toggle').dropdown();
    });*/
    
    $('.table').addClass('table-hover');
    $(document).on('pjax:success', function() {
        $('.table').addClass('table-hover');
    });
JS
);

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
        $create = [];

        if (Yii::$app->getModule('admin/pages', false)) {
            $create[] = [
                'label' => Yii::t('app/modules/admin', 'Page'),
                'url' => ['/admin/pages/pages/create']
            ];
        }

        if (Yii::$app->getModule('admin/media', false)) {
            $create[] = [
                'label' => Yii::t('app/modules/admin', 'Media item'),
                'url' => ['/admin/media/list/upload']
            ];
        }

        if (Yii::$app->getModule('admin/content', false)) {
            $create[] = [
                'label' => Yii::t('app/modules/admin', 'Content block'),
                'url' => ['/admin/content/blocks/create']
            ];
            $create[] = [
                'label' => Yii::t('app/modules/admin', 'Content list'),
                'url' => ['/admin/content/lists/create']
            ];
        }

        if (Yii::$app->getModule('admin/news', false)) {
            $create[] = [
                'label' => Yii::t('app/modules/admin', 'News'),
                'url' => ['/admin/news/news/create']
            ];
        }

        if (Yii::$app->getModule('admin/blog', false)) {
            $create[] = [
                'label' => Yii::t('app/modules/admin', 'Post'),
                'url' => ['/admin/blog/posts/create']
            ];
        }

        if (Yii::$app->getModule('admin/subscribers', false)) {
            $create[] = [
                'label' => Yii::t('app/modules/admin', 'Subscriber'),
                'url' => ['/admin/subscribers/all/create']
            ];
        }

        if (Yii::$app->getModule('admin/newsletters', false)) {
            $create[] = [
                'label' => Yii::t('app/modules/admin', 'Newsletter'),
                'url' => ['/admin/newsletters/list/create']
            ];
        }

        if (Yii::$app->getModule('admin/forms', false)) {
            $create[] = [
                'label' => Yii::t('app/modules/admin', 'Form'),
                'url' => ['/admin/forms/list/create']
            ];
        }

        if (Yii::$app->getModule('admin/users', false)) {
            $create[] = [
                'label' => Yii::t('app/modules/admin', 'User'),
                'url' => ['/admin/users/users/create/']
            ];
        }

        if (Yii::$app->getModule('admin/tasks', false)) {
            $create[] = [
                'label' => Yii::t('app/modules/admin', 'Task'),
                'url' => ['/admin/tasks/item/create/']
            ];
        }

        if (Yii::$app->getModule('admin/translations', false)) {
            $create[] = [
                'label' => Yii::t('app/modules/admin', 'Translate'),
                'url' => ['/admin/translations/list/create/']
            ];
        }

        if (count($create) > 0) {
            $items[] = [
                'label' => '<span class="fa fa-fw fa-plus"></span> ' . Yii::t('app/modules/admin', 'Add new'),
                'items' => $create
            ];
        }

        if (Yii::$app->getModule('admin/terminal', false))
            $items[] = [
                'label' => '<span class="fa fa-fw fa-terminal"></span> ' . Yii::t('app/modules/admin', 'Terminal'),
                'url' => '#terminal'
            ];

        $items[] = [
            'label' => '<span class="fa fa-fw fa-language"></span> ' . Yii::t('app/modules/admin', 'Language'),
            'items' => $this->params['langs']
        ];

        if (Yii::$app->user->isGuest)
            $items[] = [
                'label' => '<span class="fa fa-fw fa-sign-in-alt"></span> ' . Yii::t('app/modules/admin', 'Login'),
                'url' => ['/admin/login']
            ];
        else
            $items[] = [
                'label' => '<span class="fa fa-fw fa-user-circle"></span> ' . Yii::t('app/modules/admin', 'Logout') . ' (' . Yii::$app->user->identity->username . ')',
                'url' => ['/admin/logout'], 'linkOptions' => ['data-method' => 'post']
            ];


        echo Nav::widget([
            'options' => [
                'class' => 'navbar-nav navbar-left',
            ],
            'items' => [
                [
                    'label' => '<span class="fa fa-fw fa-globe"></span> '.Yii::$app->name,
                    'url' => Url::base(true),
                    'linkOptions' => ['target' => '_blank']
                ],
            ],
            'encodeLabels' => false
        ]);

        echo Nav::widget([
            'options' => [
                'id' => 'mainNav',
                'class' => 'navbar-nav navbar-right'
            ],
            'items' => $items,
            'encodeLabels' => false
        ]);

        if (!is_null(Yii::$app->dashboard->search)) {

            $searchForm = ActiveForm::begin([
                'id' => 'adminSearchForm',
                'method' => 'GET',
                'options' => [
                    'style' => 'margin-bottom: 0px !important',
                    'class' => 'navbar-form navbar-right'
                ]
            ]);
            ?>
            <div class="navbar-search">
                <?= $searchForm->field(Yii::$app->dashboard->search, 'query', [
                    'template' => '{label}<div class="input-group"><div class="input-group-addon"><span class="fa fa-search"></span></div>{input}</div>{hint}{error}'
                ])->textInput(['placeholder' => Yii::t('app/modules/admin', 'Type to search...'), "autocomplete" => "off"])->label(false); ?>
                <div class="search-box">
                    <ul class="list-unstyled"></ul>
                    <div class="no-search-results" style="display: none;">
                        <div class="alert alert-warning" role="alert">
                            <i class="fa fa-exclamation-triangle"></i>
                            <?= Yii::t('app/modules/admin', 'No entry for <strong>`<span class="query"></span>`</strong> was found.') ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php
            ActiveForm::end();
        }

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
                <div class="col-xs-12 col-sm-3 col-md-2 <?= (isset($this->params['sidebar']['options']['class'])) ? $this->params['sidebar']['options']['class'] : "sidebar" ?>">
                    <?= Nav::widget([
                        'id' => 'sidebarNav',
                        'options' => ['class' => 'nav nav-sidebar'],
                        'items' => Yii::$app->dashboard->getSidebarMenuItems(),
                        'activateParents' => true,
                        'dropDownCaret' => '<span class="fa fa-fw fa-angle-down"></span>',
                        'encodeLabels' => false
                    ]); ?>
                </div>
                <div class="col-xs-12 col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 <?= (isset($this->params['main']['options']['class'])) ? $this->params['main']['options']['class'] : "main" ?>">
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

    <?php /*$this->registerJs(<<< JS
        $(document).ready(function() {
        
            setInterval(function() {
                $.ajax({
                    type: "POST",
                    url: "/admin/checkpoint",
                    dataType: "json",
                    complete: function(data) {
                        if(data) {
                            if (data.status == 200 && data.responseJSON.loggedin) {
                                return true;
                            }
                        }
                        window.location.href = "/admin/login";
                    }
                });
            }, 10000);
            
        });
JS
    );*/ ?>

    <?php
        // Register dashboard search assets
        if (!is_null(Yii::$app->dashboard->search)) {
            $this->registerJs(<<< JS
                $(document).ready(function() {
                    
                    if ($("#adminSearchForm").length) {
                    
                        var timeout = 0;
                        var query = "";
                        var searchForm = $("#adminSearchForm");
                        
                        searchForm.find("input").on("keyup", function(event) {
                            query = $(event.target).val();
                            if (query.length >= 3) {
                                clearTimeout(timeout);
                                timeout = setTimeout(function() {
                                    searchForm.submit();
                                }, 1000);
                            }
                        });
                        
                        searchForm.on("submit", function(event) {
                            event.preventDefault();
                        
                            $.ajax({
                                type: "POST",
                                url: "/admin/search",
                                data: {
                                    query: query
                                },
                                dataType: "json",
                                beforeSend: function () {
                                    searchForm.find(".search-box").addClass("show").addClass("loading");
                                },
                                complete: function(data) {
                                    
                                    searchForm.find(".search-box > ul").empty();
                                    searchForm.find(".search-box").removeClass("loading");
                                    
                                    if (data.status == 200 && data.responseJSON.results) {
                                        
                                        if (data.responseJSON.results.length) {
                                            $.each(data.responseJSON.results, function(index, result) {
                                                var item = $("<li class=\"list-item\" />");
                                                var header = $("<h5 class=\"item-header\" />");
                                                var buttons = $("<div class=\"btn-group btn-group-xs\" role=\"group\" />");
                                                
                                                if (result.title) {
                                                    header.html(result.title);
                                                }
                                                
                                                if (!(typeof result.status == "undefined")) {
                                                    if (result.status == "1") {
                                                        $("<span class=\"label label-primary\">Published</span>").prependTo(header);
                                                    } else {
                                                        $("<span class=\"label label-default\">Draft</span>").prependTo(header);
                                                    }
                                                }
                                                
                                                item.append(header);
                                                
                                                if (result.snippet) {
                                                    $("<div class=\"item-snippet\">" + result.snippet + "</div>").appendTo(item);
                                                }
                                                
                                                if (result.url.view) {
                                                    $("<a href=\"" + result.url.view + "\" class=\"btn btn-link\" data-pajax=\"0\"><span class=\"glyphicon glyphicon-eye-open\"></span> View</a>").appendTo(buttons);
                                                }
                                                
                                                if (result.url.update) {
                                                    $("<a href=\"" + result.url.update + "\" class=\"btn btn-link\" data-pajax=\"0\"><span class=\"glyphicon glyphicon-pencil\"></span> Update</a>").appendTo(buttons);
                                                }
                                                
                                                if (result.url.public && !(typeof result.status == "undefined")) {
                                                    if (result.status == "1") {
                                                        $("<a href=\"" + result.url.public + "\" class=\"btn btn-link\" target=\"_blank\"><span class=\"glyphicon glyphicon-globe\"></span> Public</a>").appendTo(buttons);
                                                    }
                                                }
                                                
                                                item.append(buttons);
                                                searchForm.find(".search-box > ul").append(item);
                                            });
                                            searchForm.find(".search-box > ul").fadeIn();
                                            searchForm.find(".search-box .no-search-results").fadeOut();
                                            
                                            searchForm.find(".search-box").on("mouseover", function () {
                                                $(this).find(".search-box").fadeOut();
                                            });
                                        } else {
                                            searchForm.find(".search-box .no-search-results strong > span.query").text(query);
                                            searchForm.find(".search-box .no-search-results").fadeIn();
                                        }
                                    } else {
                                        searchForm.find(".search-box").removeClass("show");
                                    }
                                    
                                }
                            }).fail(function() {
                                searchForm.find("input").val();
                                searchForm.find(".search-box > ul").empty();
                                searchForm.find(".search-box").removeClass("show").removeClass("loading");
                                searchForm.find(".search-box .no-search-results").fadeOut();
                            });
                            
                        });
                        
                        searchForm.find(".search-box").click(function() {
                            searchForm.find("input").val();
                            searchForm.find(".search-box > ul").empty();
                            searchForm.find(".search-box").removeClass("show").removeClass("loading");
                            searchForm.find(".search-box .no-search-results").fadeOut();
                        });
                        
                        searchForm.find(".search-box").hover(function() {
                            searchForm.find("input").blur();
                        }, function() {
                            searchForm.find("input").val();
                            searchForm.find(".search-box > ul").empty();
                            searchForm.find(".search-box").removeClass("show").removeClass("loading");
                            searchForm.find(".search-box .no-search-results").fadeOut();
                        });
                        
                    }
                    
                });
JS
            );
        }
    ?>
    <?php
        // Register dashboard terminal assets
        if (Yii::$app->getModule('admin/terminal', false)) {
            $url = Url::to(['terminal/terminal/index']);
            $this->registerJs(<<< JS
                $(function() {
                    $('body').delegate('a[href="#terminal"]', 'click', function(event) {
                        event.preventDefault();
                        $.get(
                            '$url',
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
                });
JS
            );
        }
    ?>

    <?php yii\bootstrap\Modal::begin([
        'id' => 'terminalModal',
        'size' => 'modal-lg',
        'options' => [
            'class' => 'modal terminal-modal',
        ],
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

    <?php
        $url = Url::to(['admin/bugreport']);
        $this->registerJs(<<< JS
            $('body').delegate('a[href="#bugreport"]', 'click', function(event) {
                event.preventDefault();
                $.get(
                    '$url',
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
