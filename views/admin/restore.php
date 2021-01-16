<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model wdmg\users\models\UsersPasswordRequest */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use wdmg\admin\AdminAsset;
use yii\widgets\Pjax;

$this->title = Yii::t('app/modules/admin', 'Restore Password');
$this->params['breadcrumbs'][] = $this->title;
$bundle = AdminAsset::register($this);
?>
<div class="admin-login">
    <div class="page-title">
        <h3><?= Yii::t('app/modules/admin', 'Restore Password') ?></h3>
    </div>
    <?php Pjax::begin(['id' => 'ajaxRestorePasswordForm']); ?>
    <?php $form = ActiveForm::begin([
        'id' => 'restorePasswordForm',
        'layout' => 'horizontal',
        'fieldConfig' => [
            'template' => "<div class=\"col-xs-12\">{input}</div>\n<div class=\"col-lg-12\"><small>{error}</small></div>",
            'labelOptions' => ['class' => 'col-lg-4 control-label'],
        ],
    ]); ?>
    <?= $form->field($model, 'email')->textInput(['placeholder' => Yii::t('app/modules/admin', 'E-mail')]) ?>
    <?= Html::submitButton(Yii::t('app/modules/admin', 'Submit'), [
        'class' => 'btn btn-block btn-primary',
        'name' => 'restore-button',
        'data' => [
            'loading-text' => Yii::t('app/modules/admin', 'Submit...')
        ]
    ]) ?>
    <hr/>
    <p class="text-center"><?= Html::a(Yii::t('app/modules/admin', '&larr; Back to login'), ['admin/login']) ?></p>
    <?php ActiveForm::end(); ?>
    <?php Pjax::end(); ?>
</div>
