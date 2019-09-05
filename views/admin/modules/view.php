<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model wdmg\redirects\models\Redirects */

\yii\web\YiiAsset::register($this);

?>
<div class="admin-modules-view">

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'name:ntext',
            'description:ntext',

            'class:ntext',
            'bootstrap:ntext',
            'require:ntext',
            'type:ntext',

            'homepage:ntext',
            'support:ntext',
            'authors:ntext',
            'license:ntext',
            [
                'attribute' => 'version',
                'format' => 'raw',
                'value' => function($data) use ($module) {

                    if ($new_version = $module->checkUpdates($data->name, $data->version))
                        return $data->version . ' <label class="label label-danger">Available update to ' . $new_version . '</label>';
                    else
                        return $data->version;

                }
            ],

            'options:ntext',

            'status:ntext',
            'protected:ntext',
            'priority:ntext',

            'created_at:datetime',
            'created_by:ntext',
            'updated_at:datetime',
            'updated_by:ntext',

        ],
    ]) ?>
    <div class="modal-footer">
        <?= Html::a(Yii::t('app/modules/admin', 'Close'), "#", [
            'class' => 'btn btn-default pull-left',
            'data-dismiss' => 'modal'
        ]); ?>
        <?php
            if (!$model->protected == 1) {
                if ($model->status == $model::MODULE_STATUS_ACTIVE) {
                    echo Html::a(Yii::t('app/modules/admin', 'Disable'), Url::to(['admin/modules', 'action' => 'disable', 'id' => $model->id]), [
                        'class' => 'btn btn-danger pull-right',
                        'target' => '_self',
                        'data' => [
                            'confirm' => Yii::t('app/modules/admin', 'Are you sure you want to disable this module?'),
                        ]
                    ]);
                } elseif ($model->status == $model::MODULE_STATUS_DISABLED) {
                    echo Html::a(Yii::t('app/modules/admin', 'Activate'), Url::to(['admin/modules', 'action' => 'activate', 'id' => $model->id]), [
                        'class' => 'btn btn-success pull-right',
                        'target' => '_self',
                        'data' => [
                            'confirm' => Yii::t('app/modules/admin', 'Are you sure you want to activate this module?'),
                        ]
                    ]);
                }
            }
        ?>
    </div>
</div>
