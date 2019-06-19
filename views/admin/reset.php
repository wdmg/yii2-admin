<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model wdmg\users\models\UsersPasswordRequest */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = Yii::t('app/modules/admin', 'Reset Password');
$this->params['breadcrumbs'][] = $this->title;
//use app\assets\AppAsset;
use wdmg\admin\AdminAsset;

//AppAsset::register($this);
$bundle = AdminAsset::register($this);
?>
<div class="admin-login">
    <div class="page-title">
        <h3><?= Yii::t('app/modules/admin', 'Set your new password') ?></h3>
    </div>
    <?php $form = ActiveForm::begin([
        'id' => 'resetPasswordForm',
        'layout' => 'horizontal',
        'fieldConfig' => [
            'template' => "<div class=\"col-xs-12\">{input}</div>\n<div class=\"col-lg-12\"><small>{error}</small></div>",
            'labelOptions' => ['class' => 'col-lg-4 control-label'],
        ],
    ]); ?>
    <?= $form->field($model, 'password', [
        'template' => '<div class="col-xs-12"><div class="input-group">{input}
            <a href="#" id="showInputPassword" class="input-group-addon"><span class="fa fa-eye"></span></a></div>{error}{hint}</div>'
    ])->passwordInput(['placeholder' => Yii::t('app/modules/admin', 'New password')]); ?>
    <b><?= Yii::t('app/modules/admin', 'Please use at least:') ?></b>
    <ul class="list-unstyled password-rules">
        <li><?= Yii::t('app/modules/admin', '8 characters') ?></li>
        <li><?= Yii::t('app/modules/admin', '1 number') ?></li>
        <li><?= Yii::t('app/modules/admin', '1 lowercase letter') ?></li>
        <li><?= Yii::t('app/modules/admin', '1 uppercase letter') ?></li>
        <li><?= Yii::t('app/modules/admin', '1 special character') ?></li>
    </ul>
    <?= Html::submitButton(Yii::t('app/modules/admin', 'Submit'), ['class' => 'btn btn-block btn-primary', 'name' => 'reset-button']) ?>
    <hr/>
    <p class="text-center"><?= Html::a(Yii::t('app/modules/admin', '&larr; Back to login'), ['/admin/login']) ?></p>
    <?php ActiveForm::end(); ?>
</div>
