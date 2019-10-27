<?php

use yii\helpers\Html;
use wdmg\widgets\ChartJS;

/* @var $this yii\web\View */

$this->title = $this->context->module->name;
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="page-header">
    <h1>
        <?= Html::encode($this->title) ?> <small class="text-muted pull-right">[v.<?= $this->context->module->version ?>]</small>
    </h1>
</div>
<div class="admin-index">
    <div class="row">

    <?php
        if ($intance = $module->moduleLoaded('admin/pages', true)) {
    ?>
        <div class="col-xs-12 col-sm-6 col-md-4">
            <div class="panel panel-widget">
                <div class="panel-heading">
                    <?= Yii::t('app/modules/pages', 'Pages') ?>
                </div>
                <?php

                    if (isset($widgets['recentPages'])) {
                        if (count($widgets['recentPages']) > 0) {
                            echo '<ul class="panel-body list-group">';
                            foreach ($widgets['recentPages'] as $item) {
                                $username = isset($item['user']['username']) ? $item['user']['username'].', ' : '';
                                echo '<li class="list-group-item">'.Html::a($item['name'], ['./pages/pages/update', 'id' => $item['id']]);
                                echo Html::tag('small', $username . Yii::$app->formatter->asDate($item['updated_at']), ['class' => 'pull-right text-muted']);
                                echo '</li>';
                            }
                            echo '</ul>';
                        } else {
                            echo '<div class="panel-body">';
                            echo '<p class="text-center text-muted">'.Yii::t('app/modules/pages', 'No pages available for display').'</p>';
                            echo '</div>';
                        }
                    } else {
                        echo '<div class="panel-body">';
                        echo '<p class="text-center text-warning">'.Yii::t('app/modules/pages', 'An error occurred while retrieving the page list').'</p>';
                        echo '</div>';
                    }
                ?>
                <div class="panel-footer">
                    <?= Html::a(Yii::t('app/modules/admin', 'View all'), ['./pages'], ['class' => 'text-muted']) ?>
                    <?= Html::a('<span class="fa fa-plus"></span> ' . Yii::t('app/modules/pages', 'Add new page'), ['./pages/create'], ['class' => 'pull-right']) ?>
                </div>
            </div>
        </div>
    <?php
        }
    ?>

    <?php
        if ($intance = $module->moduleLoaded('admin/news', true)) {
    ?>
        <div class="col-xs-12 col-sm-6 col-md-4">
            <div class="panel panel-widget">
                <div class="panel-heading">
                    <?= Yii::t('app/modules/news', 'News') ?>
                </div>
                <?php
                    if (isset($widgets['recentNews'])) {
                        if (count($widgets['recentNews']) > 0) {
                            echo '<ul class="panel-body list-group">';
                            foreach ($widgets['recentNews'] as $item) {
                                $username = isset($item['user']['username']) ? $item['user']['username'].', ' : '';
                                echo '<li class="list-group-item">'.Html::a($item['name'], ['./news/news/update', 'id' => $item['id']]);
                                echo Html::tag('small', $username . Yii::$app->formatter->asDate($item['updated_at']), ['class' => 'pull-right text-muted']);
                                echo '</li>';
                            }
                            echo '</ul>';
                        } else {
                            echo '<div class="panel-body">';
                            echo '<p class="text-center text-muted">'.Yii::t('app/modules/news', 'No news available').'</p>';
                            echo '</div>';
                        }
                    } else {
                        echo '<div class="panel-body">';
                        echo '<p class="text-center text-warning">'.Yii::t('app/modules/news', 'An error occurred while retrieving the news list').'</p>';
                        echo '</div>';
                    }
                ?>
                <div class="panel-footer">
                    <?= Html::a(Yii::t('app/modules/admin', 'View all'), ['./news'], ['class' => 'text-muted']) ?>
                    <?= Html::a('<span class="fa fa-plus"></span> ' . Yii::t('app/modules/news', 'Add news item'), ['./news/create'], ['class' => 'pull-right']) ?>
                </div>
            </div>
        </div>
    <?php
        }
    ?>

    <?php
        if ($intance = $module->moduleLoaded('admin/users', true)) {
    ?>
        <div class="col-xs-12 col-sm-6 col-md-4">
            <div class="panel panel-widget">
                <div class="panel-heading">
                    <?= Yii::t('app/modules/users', 'Users') ?>
                </div>
                <?php
                    if (isset($widgets['lastUsers'])) {
                        if (count($widgets['lastUsers']) > 0) {
                            echo '<ul class="panel-body list-group">';
                            foreach ($widgets['lastUsers'] as $item) {
                                echo '<li class="list-group-item">'.Html::a($item['username'], ['./users/users/update', 'id' => $item['id']]);
                                echo Html::tag('small', \Yii::$app->formatter->asDate($item['created_at']), ['class' => 'pull-right text-muted']);
                                echo '</li>';
                            }
                            echo '</ul>';
                        } else {
                            echo '<div class="panel-body">';
                            echo '<p class="text-center text-muted">'.Yii::t('app/modules/users', 'No users available').'</p>';
                            echo '</div>';
                        }
                    } else {
                        echo '<div class="panel-body">';
                        echo '<p class="text-center text-warning">'.Yii::t('app/modules/users', 'An error occurred while retrieving the users list').'</p>';
                        echo '</div>';
                    }
                ?>
                <div class="panel-footer">
                    <?= Html::a(Yii::t('app/modules/admin', 'View all'), ['./users'], ['class' => 'text-muted']) ?>
                    <?= Html::a('<span class="fa fa-plus"></span> ' . Yii::t('app/modules/users', 'Add new user'), ['./users/create'], ['class' => 'pull-right']) ?>
                </div>
            </div>
        </div>
    <?php
        }
    ?>

    <?php
        if ($intance = $module->moduleLoaded('admin/reviews', true)) {
    ?>
        <div class="col-xs-12 col-sm-6 col-md-4">
            <div class="panel panel-widget">
                <div class="panel-heading">
                    <?= Yii::t('app/modules/reviews', 'Reviews') ?>
                </div>
                <?php
                    if (isset($widgets['recentReviews'])) {
                        if (count($widgets['recentReviews']) > 0) {
                            echo '<ul class="panel-body list-group">';
                            /*foreach ($widgets['recentReviews'] as $item) {
                                echo '<li class="list-group-item">'.Html::a($item['name'], ['./reviews/reviews/update', 'id' => $item['id']]);
                                echo Html::tag('small', \Yii::$app->formatter->asDatetime($item['created_at']), ['class' => 'pull-right text-muted']);
                                echo '</li>';
                            }*/
                            echo '</ul>';
                        } else {
                            echo '<div class="panel-body">';
                            echo '<p class="text-center text-muted">'.Yii::t('app/modules/reviews', 'No reviews available').'</p>';
                            echo '</div>';
                        }
                    } else {
                        echo '<div class="panel-body">';
                        echo '<p class="text-center text-warning">'.Yii::t('app/modules/reviews', 'An error occurred while retrieving the reviews list').'</p>';
                        echo '</div>';
                    }
                ?>
                <div class="panel-footer">
                    <?= Html::a(Yii::t('app/modules/admin', 'View all'), ['./reviews'], ['class' => 'text-muted']) ?>
                </div>
            </div>
        </div>
    <?php
        }
    ?>
    <?php
        if ($intance = $module->moduleLoaded('admin/comments', true)) {
    ?>
        <div class="col-xs-12 col-sm-6 col-md-4">
            <div class="panel panel-widget">
                <div class="panel-heading">
                    <?= Yii::t('app/modules/comments', 'Comments') ?>
                </div>
                <?php
                    if (isset($widgets['recentComments'])) {
                        if (count($widgets['recentComments']) > 0) {
                            echo '<ul class="panel-body list-group">';
                            /*foreach ($widgets['recentComments'] as $item) {
                                echo '<li class="list-group-item">'.Html::a($item['name'], ['./comments/comments/update', 'id' => $item['id']]);
                                echo Html::tag('small', \Yii::$app->formatter->asDatetime($item['created_at']), ['class' => 'pull-right text-muted']);
                                echo '</li>';
                            }*/
                            echo '</ul>';
                        } else {
                            echo '<div class="panel-body">';
                            echo '<p class="text-center text-muted">'.Yii::t('app/modules/comments', 'No comments available').'</p>';
                            echo '</div>';
                        }
                    } else {
                        echo '<div class="panel-body">';
                        echo '<p class="text-center text-warning">'.Yii::t('app/modules/comments', 'An error occurred while retrieving the comments list').'</p>';
                        echo '</div>';
                    }
                ?>
                <div class="panel-footer">
                    <?= Html::a(Yii::t('app/modules/admin', 'View all'), ['./comments'], ['class' => 'text-muted']) ?>
                </div>
            </div>
        </div>
    <?php
        }
    ?>
    <?php
        if ($intance = $module->moduleLoaded('admin/stats', true)) {
    ?>
        <div class="col-xs-12 col-sm-6 col-md-4">
            <div class="panel panel-widget">
                <div class="panel-heading">
                    <?= Yii::t('app/modules/stats', 'Statistics') ?>
                </div>
                <?php
                    if (isset($widgets['recentStats'])) {
                        if (count($widgets['recentStats']) > 0) {
                            echo ChartJS::widget([
                                'type' => 'line',
                                'options' => [
                                    'width' => 640,
                                    'height' => 260
                                ],
                                'data' => $widgets['recentStats']
                            ]);
                        } else {
                            echo '<div class="panel-body">';
                            echo '<p class="text-center text-muted">'.Yii::t('app/modules/stats', 'No stats data available').'</p>';
                            echo '</div>';
                        }
                    } else {
                        echo '<div class="panel-body">';
                        echo '<p class="text-center text-warning">'.Yii::t('app/modules/stats', 'An error occurred while retrieving the stats data').'</p>';
                        echo '</div>';
                    }
                ?>
                <div class="panel-footer">
                    <?= Html::a(Yii::t('app/modules/admin', 'View all'), ['./stats'], ['class' => 'text-muted']) ?>
                </div>
            </div>
        </div>
    <?php
        }
    ?>




    </div>
</div>

<?php echo $this->render('../_debug'); ?>
