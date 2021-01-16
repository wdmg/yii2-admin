<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model wdmg\users\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use wdmg\admin\AdminAsset;
use yii\widgets\Pjax;

$this->title = Yii::t('app/modules/admin', 'SignIn');
$this->params['breadcrumbs'][] = $this->title;
$bundle = AdminAsset::register($this);
?>
<div class="admin-login">
    <?= Html::img($bundle->baseUrl . '/images/logotype-welcome.svg', [
        'class' => "logotype img-responsive",
        'onerror' => "this.src='" . $bundle->baseUrl . '/images/logotype-welcome.png' . "'"
    ]); ?>
    <?php Pjax::begin(['id' => 'ajaxLoginForm']); ?>
    <?php $form = ActiveForm::begin([
        'id' => 'loginForm',
        'layout' => 'horizontal',
        'options' => [
            'data-pjax' => true
        ],
        'fieldConfig' => [
            'template' => "<div class=\"col-xs-12\">{input}</div>\n<div class=\"col-lg-12\"><small>{error}</small></div>",
            'labelOptions' => ['class' => 'col-lg-4 control-label'],
        ],
    ]); ?>
    <?= $form->field($model, 'username')->textInput(['placeholder' => Yii::t('app/modules/admin', 'Username')]) ?>
    <?= $form->field($model, 'password', [
        'template' => '<div class="col-xs-12"><div class="input-group">{input}
            <a href="#" id="showInputPassword" class="input-group-addon"><span class="fa fa-eye"></span></a></div>{error}{hint}</div>'
    ])->passwordInput(['placeholder' => Yii::t('app/modules/admin', 'Password')]); ?>

    <?= $form->field($model, 'rememberMe')->checkbox([
        'template' => "<div class=\"col-xs-12\">{input} - {label}</div>\n<div class=\"col-xs-12\"><small>{error}</small></div>",
    ])->label(Yii::t('app/modules/admin', 'Remember Me')) ?>
    <?= Html::submitButton(Yii::t('app/modules/admin', 'SignIn'), [
        'class' => 'btn btn-block btn-primary',
        'name' => 'login-button',
        'data' => [
            'loading-text' => Yii::t('app/modules/admin', 'Authentication...')
        ]
    ]) ?>
    <hr/>
    <p class="text-center"><?= Yii::t('app/modules/admin', 'Don`t remember password? You may {link}.', [
        'link' => Html::a(Yii::t('app/modules/admin', 'restore it here'), ['admin/restore']),
    ]) ?></p>
    <?php ActiveForm::end(); ?>
    <?php Pjax::end(); ?>
</div>