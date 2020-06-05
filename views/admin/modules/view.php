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
            [
                'attribute' => 'description',
                'value' => function($data) use ($module) {
                    return Yii::t('app/modules/'.$data->module, $data->description);
                }
            ],

            'class:ntext',
            // 'bootstrap:ntext',
            [
                'attribute' => 'require',
                'format' => 'raw',
                'contentOptions' => [
                    'style' => "word-break:break-all;"
                ]
            ],
            [
                'attribute' => 'type',
                'format' => 'raw',
                'contentOptions' => [
                    'style' => "word-break:break-all;"
                ]
            ],
            [
                'attribute' => 'homepage',
                'format' => 'raw',
                'contentOptions' => [
                    'style' => "word-break:break-all;"
                ]
            ],
            [
                'attribute' => 'support',
                'format' => 'raw',
                'contentOptions' => [
                    'style' => "word-break:break-all;"
                ]
            ],
            [
                'attribute' => 'authors',
                'format' => 'raw',
                'contentOptions' => [
                    'style' => "word-break:break-all;"
                ]
            ],

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
            [
                'attribute' => 'options',
                'format' => 'raw',
                'contentOptions' => [
                    'style' => "word-break:break-all;"
                ]
            ],

            'status:ntext',
            'protected:ntext',
            'priority:ntext',

            [
                'attribute' => 'created_at',
                'label' => Yii::t('app/modules/admin','Created'),
                'format' => 'html',
                'value' => function($data) {

                    $output = "";
                    if ($user = $data->createdBy) {
                        $output = Html::a($user->username, ['users/view', 'id' => $user->id], [
                            'target' => '_blank',
                            'data-pjax' => 0
                        ]);
                    } else if ($data->created_by) {
                        $output = $data->created_by;
                    }

                    if (!empty($output))
                        $output .= ", ";

                    $output .= Yii::$app->formatter->format($data->created_at, 'datetime');
                    return $output;
                }
            ],

            [
                'attribute' => 'updated_at',
                'label' => Yii::t('app/modules/admin','Updated'),
                'format' => 'html',
                'value' => function($data) {

                    $output = "";
                    if ($user = $data->updatedBy) {
                        $output = Html::a($user->username, ['users/view', 'id' => $user->id], [
                            'target' => '_blank',
                            'data-pjax' => 0
                        ]);
                    } else if ($data->updated_by) {
                        $output = $data->updated_by;
                    }

                    if (!empty($output))
                        $output .= ", ";

                    $output .= Yii::$app->formatter->format($data->updated_at, 'datetime');
                    return $output;
                }
            ],

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
