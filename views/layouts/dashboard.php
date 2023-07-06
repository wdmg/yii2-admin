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
<body class="dashboard<?= (YII_ENV_DEV) ? ' env-dev' : '' ?><?= (YII_DEBUG) ? ' env-debug' : '' ?>">
    <?php $this->beginBody() ?>
    <div class="<?= 'module-' . $this->context->module->id; ?> <?= 'action-' . $this->context->action->id; ?>">
        <?php
        NavBar::begin([
            'brandLabel' => Html::img($bundle->baseUrl . '/images/logotype-inline.svg', [
                'class' => "img-responsive",
                'onerror' => "this.src='" . $bundle->baseUrl . '/images/logotype-inline.png' . "'"
            ]),
            'brandUrl' => ['/admin/admin/index'],
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
        if ($createMenuItems = Yii::$app->dashboard->getCreateMenuItems()) {
            $items[] = [
                'label' => '<span class="fa fa-fw fa-plus"></span> ' . Yii::t('app/modules/admin', 'Add new'),
                'items' => $createMenuItems
            ];
        }

        if (Yii::$app->getModule('admin/terminal', false))
            $items[] = [
                'label' => '<span class="fa fa-fw fa-terminal"></span> ' . Yii::t('app/modules/admin', 'Terminal'),
                'url' => '#terminal',
                'linkOptions' => [
                    'data-route' => Url::to(['/admin/terminal/terminal'])
                ]
            ];

        if (isset($this->params['favourites'])) {

            $favourites = [];
            if (is_array($this->params['favourites'])) {
	            foreach ($this->params['favourites'] as $item) {
		            $favourites[] = [
			            'url' => $item['url'],
			            'label' => $item['label'] . '<i class="glyphicon glyphicon-trash" title="'.Yii::t('app/modules/admin', 'Remove').'" data-label="'.$item['label'].'" data-url="'.$item['url'].'"></i>',
		            ];
	            }
            }

            $items[] = [
                'label' => '<span class="fa fa-fw fa-star"></span> ' . Yii::t('app/modules/admin', 'Favourites'),
                'items' => $favourites,
                'options' => [
                    'class' => 'favourites'
                ]
            ];
        }

        if (isset($this->params['langs'])) {
            $items[] = [
                'label' => '<span class="fa fa-fw fa-language"></span> ' . Yii::t('app/modules/admin', 'Language'),
                'items' => $this->params['langs']
            ];
        }

        if (Yii::$app->user->isGuest)
            $items[] = [
                'label' => '<span class="fa fa-fw fa-sign-in-alt"></span> ' . Yii::t('app/modules/admin', 'Login'),
                'url' => ['/admin/admin/login']
            ];
        else
            $items[] = [
                'label' => '<span class="fa fa-fw fa-user-circle"></span> ' . Yii::t('app/modules/admin', 'Logout') . ' (' . Yii::$app->user->identity->username . ')',
                'url' => ['/admin/admin/logout'], 'linkOptions' => ['data-method' => 'post']
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

        echo '<div id="time-clock" class="navbar-text navbar-right navbar-clock"></div>';

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
                    <?php

                        // Breadcrumbs links
                        $links = isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [];

                        // Add to favourites link
                        $in_favourites = false;
                        $favourites = $this->params['favourites'];
                        if (is_array($favourites)) {
                            foreach ($favourites as $key => &$row) {
                                if ($row['url'] == Yii::$app->request->url) {
	                                $in_favourites = true;
                                    break;
                                }
                            }
                        }

                        // Get title for favourites
                        $favourite_title = $this->title;
                        if (!empty($this->params['breadcrumbs'])) {
	                        $last_breads_key = \wdmg\helpers\ArrayHelper::keyLast($this->params['breadcrumbs']);
                            if ($last_breads_key) {
	                            $last_of_breads = $this->params['breadcrumbs'][$last_breads_key];
	                            if (!is_array($last_of_breads) && !empty($last_of_breads)) {
		                            if ($this->title == $last_of_breads) {

			                            if ($this->context->module->name !== $last_of_breads)
				                            $favourite_title = $this->context->module->name . ' / ' . $last_of_breads;

		                            } else {
			                            $favourite_title .= ' / ' . $last_of_breads;
		                            }
	                            }
                            }
                        }

                        if (!empty($favourite_title) && !empty(Yii::$app->request->url)) {
	                        $links[] = [
		                        'label' => ($in_favourites) ? '<span class="glyphicon glyphicon-star"></span> ' . Yii::t('app/modules/admin', 'Un Favourite') : '<span class="glyphicon glyphicon-star-empty"></span> ' . Yii::t('app/modules/admin', 'Favourite'),
		                        'url' => '#favourites',
		                        'template' => '<li class="favourites">{link}</li>',
		                        'data' => [
			                        'label' => $favourite_title,
			                        'url' => Yii::$app->request->url
		                        ]
	                        ];
                        }
                    ?>
                    <?= Breadcrumbs::widget([
                        'tag' => "ul",
                        'options' => [
                            'class' => 'breadcrumb',
                            'data-path' => Yii::$app->request->url
                        ],
	                    'encodeLabels' => false,
                        'homeLink' => [
                            'label' => Yii::t('app/modules/admin', 'Main'),
                            'url' => Url::to(['/admin/admin/index'])
                        ],
                        'links' => $links,
                    ]); ?>
                    <?= Alert::widget(); ?>
                    <?= $content; ?>
                    <?php Pjax::end(); ?>
                </div>
            </div>
        </div>
    </div>

    <?php
        /*\wdmg\messages\widgets\ChatWidget::begin();
        \wdmg\messages\widgets\ChatWidget::end();*/
    ?>

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
    $show_date = $this->params['datetime.showDate'];
    $show_time = $this->params['datetime.showTime'];
    $format_24 = $this->params['datetime.timeFormat24'];
    $server_datetime = gmdate('Y-m-d\TH:i:s\Z');
    $this->registerJs(<<< JS
        $(document).ready(function() {
            const server_datetime = new Date('$server_datetime');
            function showTime() {
                server_datetime.setSeconds(server_datetime.getSeconds() + 1);
                var hours = server_datetime.getHours(); // 0 - 23
                var minutes = server_datetime.getMinutes(); // 0 - 59
                var seconds = server_datetime.getSeconds(); // 0 - 59
                
                var session = "";
                if (!Boolean('$format_24')) {
                    session = "AM";
                
                    if (hours == 0)
                        hours = 12;
                    
                    if (hours > 12) {
                        hours = hours - 12;
                        session = "PM";
                    }
                }
                
                hours = (hours < 10) ? "0" + hours : hours;
                minutes = (minutes < 10) ? "0" + minutes : minutes;
                seconds = (seconds < 10) ? "0" + seconds : seconds;
                
                var delimiter = '<span style="opacity:1">:</span>';
                if (seconds % 2)
                    delimiter = '<span style="opacity:0">:</span>';
                
                var date = "";
                if (Boolean('$show_date'))
                    date = server_datetime.toLocaleDateString(undefined, { weekday: 'short', year: 'numeric', month: 'long', day: 'numeric' }) + " ";
                
                var time = "";
                if (Boolean('$show_time'))
                    time = hours + delimiter + minutes + delimiter + seconds + " " + session;
                
                document.getElementById("time-clock").innerHTML = date + time;
                setTimeout(showTime, 1000);
            }
            showTime(true);
        });
JS
    ); ?>

    <?php $this->registerJs(<<< JS
        $(document).ready(function() {
            $('#mainNav li.favourites .dropdown-menu li > a > .glyphicon, a[href="#favourites"]').click((event) => {
                
                event.preventDefault();
                
                if (typeof (event.target.dataset.label) !== "undefined" && typeof (event.target.dataset.url) !== "undefined") {
                   var data = {"label": event.target.dataset.label, "url": event.target.dataset.url};
                    $.ajax({
                        type: "POST",
                        url: "/admin/favourites",
                        data: data,
                        dataType: "json",
                        complete: function(data) {
                            if(data) {
                                if (data.status == 200 && data.responseJSON.success) {
                                    return true;
                                }
                            }
                            window.location.href = "/admin";
                        }
                    }); 
                }
            });
        });
JS
    ); ?>

    <?php
        // Register dashboard search assets
        if (!is_null(Yii::$app->dashboard->search)) {
            $searchUrl = Url::to(['/admin/search']);
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
                                url: "$searchUrl",
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
            $this->registerJs(<<< JS
                $(function() {
                    $('body').delegate('a[href="#terminal"]', 'click', function(event) {
                        event.preventDefault();
                        let route = event.target.dataset.route;
                        $.get(
                            route,
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
        $url = Url::to(['/admin/bugreport']);
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
