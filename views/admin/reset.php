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
        <h3><?= Yii::t('app/modules/admin', 'Reset Password') ?></h3>
    </div>
    <?php $form = ActiveForm::begin([
        'id' => 'resetPasswordForm',
        'layout' => 'horizontal',
        'fieldConfig' => [
            'template' => "<div class=\"col-xs-12\">{input}</div>\n<div class=\"col-lg-12\"><small>{error}</small></div>",
            'labelOptions' => ['class' => 'col-lg-4 control-label'],
        ],
    ]); ?>
    <?= $form->field($model, 'password')->passwordInput(['placeholder' => Yii::t('app/modules/admin', 'New password')]) ?>
    <?= Html::submitButton(Yii::t('app/modules/admin', 'Submit'), ['class' => 'btn btn-block btn-primary', 'name' => 'reset-button']) ?>
    <hr/>
    <p class="text-center"><?= Html::a(Yii::t('app/modules/admin', '&larr; Back to login'), ['/admin/login']) ?></p>
    <?php ActiveForm::end(); ?>
</div>
