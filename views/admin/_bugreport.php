<?php

use yii\helpers\Html;
use yii\widgets\Pjax;
use yii\widgets\ActiveForm;
/* @var $this yii\web\View */

?>
<?php  Pjax::begin();?>
<?php $form = ActiveForm::begin([
    'id' => "addBugreportForm",
    'action' => ['admin/bugreport'],
    'options' => [
        'enctype' => 'multipart/form-data'
    ]
]); ?>

<div class="form-group">
    <?= $form->field($model, 'name')
        ->textInput(['class' => 'form-control', 'placeholder' => 'Jonh Doe'])
        ->label(Yii::t('app/modules/admin', 'Your name'));
    ?>
</div>
<div class="form-group">
    <?= $form->field($model, 'email')
        ->textInput(['class' => 'form-control', 'placeholder' => 'yourname@example.com'])
        ->label(Yii::t('app/modules/admin', 'E-mail'));
    ?>
</div>
<div class="form-group">
    <?= $form->field($model, 'message')
        ->textarea(['class' => 'form-control', 'rows' => 6, 'placeholder' => Yii::t('app/modules/admin', 'Describe your problem or suggestion for optimization here...')])
        ->label(Yii::t('app/modules/admin', 'Message'));
    ?>
</div>
<div class="form-group">
    <?= $form->field($model, 'screenshots[]')
        ->fileInput(['class' => 'form-control', 'multiple' => true, 'accept' => 'image/*', 'placeholder' => Yii::t('app/modules/admin', 'Select a files...')])
        ->label(Yii::t('app/modules/admin', 'Screenshots'));
    ?>
</div>
<div class="form-group">
    <?= $form->field($model, 'report')
        ->textarea(['rows' => 6, 'readonly' => true, 'class' => 'form-control disabled'])
        ->label(Yii::t('app/modules/admin', 'Information to be transmitted in the report'));
    ?>
    <em class="text-danger">
        <?= Yii::t('app/modules/admin', '* - passwords and/or authorization data will not be transferred in this report!'); ?>
    </em>
</div>
<hr/>
<div class="form-group">
    <?= Html::button(Yii::t('app/modules/admin', 'Cancel'), ['class' => 'btn btn-default', 'data-dismiss' => 'modal']); ?>
    <?= Html::submitButton(Yii::t('app/modules/admin', 'Send'), [
        'class' => 'btn btn-primary pull-right',
        'data' => [
            'loading-text' => Yii::t('app/modules/admin', 'Send...')
        ]
    ]); ?>
</div>

<?php ActiveForm::end(); ?>
<?php Pjax::end();?>