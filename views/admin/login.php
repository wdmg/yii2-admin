<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Login';
$this->params['breadcrumbs'][] = $this->title;
//use app\assets\AppAsset;
use wdmg\admin\AdminAsset;

//AppAsset::register($this);
$bundle = AdminAsset::register($this);
?>
<div class="admin-login">
    <div class="row">
        <div class="col-xs-12 col-sm-12">
            <?= Html::img($bundle->baseUrl . '/images/logotype.svg', [
                'class' => "logotype img-responsive",
                'onerror' => "this.src='" . $bundle->baseUrl . '/images/logotype.png' . "'"
            ]); ?>
        </div>
    </div>
    <div class="row">
        <?php $form = ActiveForm::begin([
            'id' => 'loginForm',
            'layout' => 'horizontal',
            'fieldConfig' => [
                'template' => "<div class=\"col-xs-12\">{input}</div>\n<div class=\"col-lg-12\"><small>{error}</small></div>",
                'labelOptions' => ['class' => 'col-lg-4 control-label'],
            ],
        ]); ?>
        <?= $form->field($model, 'username')->textInput(['placeholder' => Yii::t('app/modules/admin', 'Username')]) ?>
        <?= $form->field($model, 'password')->passwordInput(['placeholder' => Yii::t('app/modules/admin', 'Password')]) ?>
        <?= $form->field($model, 'rememberMe')->checkbox([
            'template' => "<div class=\"col-xs-12\">{input} - {label}</div>\n<div class=\"col-xs-12\"><small>{error}</small></div>",
        ])->label(Yii::t('app/modules/admin', 'Remember Me')) ?>
        <?= Html::submitButton(Yii::t('app/modules/admin', 'Login'), ['class' => 'btn btn-block btn-primary', 'name' => 'login-button']) ?>
        <hr/>
        <p class="text-center"><?= Yii::t('app/modules/admin', 'Don`t remember password? You may {link}.', [
            'link' => Html::a(Yii::t('app/modules/admin', 'restore it here'), ['/admin/restore']),
        ]) ?></p>
        <?php ActiveForm::end(); ?>
    </div>
</div>
