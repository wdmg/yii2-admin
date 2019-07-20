<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\bootstrap\Modal;
use yii\widgets\ActiveForm;
use wdmg\widgets\SelectInput;

/* @var $this yii\web\View */

$this->title = Yii::t('app/modules/admin', 'Modules');
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="page-header">
    <h1>
        <?= Html::encode($this->title) ?> <small class="text-muted pull-right">[v.<?= $this->context->module->version ?>]</small>
    </h1>
</div>
<div class="admin-modules">
    <?php Pjax::begin([
        'id' => "adminModulesAjax",
        'timeout' => 5000
    ]); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'layout' => '{summary}<br\/>{items}<br\/>{summary}<br\/><div class="text-center">{pager}</div>',
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'module',
            'name',
            'description',
            'class',
            'bootstrap',
            'version',
            [
                'attribute' => 'status',
                'format' => 'raw',
                'headerOptions' => [
                    'class' => 'text-center'
                ],
                'contentOptions' => [
                    'class' => 'text-center'
                ],
                'value' => function($data) {
                    if ($data->protected) {

                        if ($data->status == $data::MODULE_STATUS_ACTIVE)
                            $status = '<span class="label label-success">' . Yii::t('app/modules/admin', 'Active') . '</span>';
                        elseif ($data->status == $data::MODULE_STATUS_DISABLED)
                            $status = '<span class="label label-default">' . Yii::t('app/modules/admin', 'Disabled') . '</span>';
                        else
                            $status = '<span class="label label-danger">' . Yii::t('app/modules/admin', 'Deleted') . '</span>';

                        return $status . ' <span class="label label-danger">' . Yii::t('app/modules/admin', 'Protected') . '</span>';

                    } else {
                        if ($data->status == $data::MODULE_STATUS_ACTIVE) {
                            return '<div id="switcher-' . $data->id . '" data-value-current="' . $data->status . '" data-id="' . $data->id . '" data-toggle="button-switcher" class="btn-group btn-toggle"><button data-value="0" class="btn btn-xs btn-default">OFF</button><button data-value="1" class="btn btn-xs btn-primary">ON</button></div>';
                        } else if ($data->status == $data::MODULE_STATUS_DISABLED) {
                            return '<div id="switcher-' . $data->id . '" data-value-current="' . $data->status . '" data-id="' . $data->id . '" data-toggle="button-switcher" class="btn-group btn-toggle"><button data-value="0" class="btn btn-xs btn-danger">OFF</button><button data-value="1" class="btn btn-xs btn-default">ON</button></div>';
                        } else {
                            return '<span class="label label-default">' . Yii::t('app/modules/admin', 'Deleted') . '</span>';
                        }
                    }
                }
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'header' => Yii::t('app/modules/admin', 'Actions'),
                'buttons'=> [
                    'view' => function($url, $data, $key) {
                        return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', Url::to(['admin/modules', 'action' => 'view', 'id' => $data['id']]), [
                            'class' => 'admin-modules-details-link',
                            'title' => Yii::t('yii', 'View'),
                            'data-toggle' => 'modal',
                            'data-target' => '#moduleDetails',
                            'data-id' => $key,
                            'data-pjax' => '1'
                        ]);
                    },
                    'delete' => function($url, $data, $key) {
                        return Html::a('<span class="glyphicon glyphicon-trash"></span>', Url::to(['admin/modules', 'action' => 'delete', 'id' => $data['id']]), [
                            'title' => Yii::t('yii', 'Delete'),
                            'data-id' => $key,
                            'data-pjax' => '0'
                        ]);
                    },
                ],
                'visibleButtons' => [
                    'update' => false,
                    'delete' => function ($model, $key, $index) use ($hasAutoload) {
                        return !($model->protected) && !($model->status == $model::MODULE_STATUS_NOT_INSTALL);
                    }
                ],
            ]
        ],
    ]); ?>
    <hr/>
    <div class="modules-add-form">
        <?php $form = ActiveForm::begin([
            'options' => [
                'class' => 'form form-inline'
            ]
        ]); ?>
            <legend><?= Yii::t('app/modules/admin', 'Available modules'); ?></legend>
            <div class="col-xs-6 col-sm-3 col-lg-3">
                <?= $form->field($model, 'extensions', [
                    'options' => [
                        'tag' => false
                    ]])->label(false)->widget(SelectInput::className(), [
                    'items' => $extensions,
                    'options' => [
                        'class' => 'form-control'
                    ],
                    'pluginOptions' => [
                        'dropdownClass' => '.dropdown .btn-block',
                        'toggleClass' => '.btn .btn-default .dropdown-toggle .btn-block',
                        'toggleText' => Yii::t('app/modules/admin', 'Modules')
                    ]
                ]); ?>
            </div>
            <div class="col-xs-6 col-sm-3 col-lg-3">
                <?= $form->field($model, 'autoActivate')->checkbox([
                    'checked' => true,
                    'style' => 'margin-top:10px;',
                ]); ?>
            </div>
            <div class="col-xs-12 col-sm-6 col-lg-3">
                <div class="form-group field-modules-autoactivate">
                    <?= Html::submitButton(Yii::t('app/modules/admin', 'Add module'), [
                        'class' => 'btn btn-success',
                        'disabled' => (count($extensions) == 0) ? true : false
                    ]) ?>
                </div>
            </div>
        <?php ActiveForm::end(); ?>
    </div>
    <?php Pjax::end(); ?>
</div>

<?php $this->registerJs(
    'var $container = $("#adminModulesAjax");
    var requestURL = window.location.href;
    if ($container.length > 0) {
        $container.delegate(\'[data-toggle="button-switcher"] button\', \'click\', function() {
            var id = $(this).parent(\'.btn-group\').data(\'id\');
            var value = $(this).data(\'value\');
             $.ajax({
                type: "POST",
                url: requestURL + \'?change=status\',
                dataType: \'json\',
                data: {\'id\': id, \'value\': value},
                complete: function(data) {
                    $.pjax.reload({type:\'POST\', container:\'#adminModulesAjax\'});
                }
             });
        });
    }', \yii\web\View::POS_READY
); ?>
<?php $this->registerJs(<<< JS
$('body').delegate('.admin-modules-details-link', 'click', function(event) {
    event.preventDefault();
    $.get(
        $(this).attr('href'),
        function (data) {
            $('#moduleDetails .modal-body').html($(data).remove('.modal-footer'));
            if ($(data).find('.modal-footer').length > 0) {
                $('#moduleDetails').find('.modal-footer').remove();
                $('#moduleDetails .modal-content').append($(data).find('.modal-footer'));
            }
            $('#moduleDetails').modal();
        }  
    );
});
JS
); ?>
<?php Modal::begin([
    'id' => 'moduleDetails',
    'header' => '<h4 class="modal-title">'.Yii::t('app/modules/admin', 'Module details').'</h4>',
    'footer' => '<a href="#" class="btn btn-default pull-left" data-dismiss="modal">'.Yii::t('app/modules/admin', 'Close').'</a>',
    'clientOptions' => [
        'show' => false
    ]
]); ?>
<?php Modal::end(); ?>

<?php echo $this->render('../../_debug'); ?>
