<?php

use yii\helpers\Html;
use yii\helpers\Url;
use wdmg\admin\AdminAsset;

/* @var $this yii\web\View */
/* @var $user \wdmg\users\models\Users */

$bundle = AdminAsset::register($this);

$resetLink = Yii::$app->urlManager->createAbsoluteUrl([$linkRoute, 'token' => $user->password_reset_token]);

if (isset(Yii::$app->params["mailer.trackingKey"]))
    $logotypeLink = Url::to(Url::home(true) . 'mail/track?url=' . $bundle->baseUrl . '/images/logotype.png&key=' . Yii::$app->params["mailer.trackingKey"]);
else
    $logotypeLink = Url::to(Url::home(true) . $bundle->baseUrl . '/images/logotype.png');

?>
<div class="password-reset">
    <p style="text-align:center;"><?= Html::a(Html::img($logotypeLink, [
        'style' => "width:160px;"
    ]), Url::home(true)); ?></p>
    <h3><?= Html::encode(Yii::t('app/modules/admin', 'Hi {username}!', [
        'username' => $user->username,
    ])); ?></h3>
    <p><?= Yii::t('app/modules/admin', 'Someone, perhaps you, requested a password reset to the administrative panel of the site {link}.', [
        'link' => Html::a(Html::encode(Url::home(true)), Url::home(true)),
    ]); ?></p>
    <p><?= Yii::t('app/modules/admin', 'Follow the link below to reset your password: {link}', [
        'link' => Html::a(Html::encode($resetLink), $resetLink),
    ]); ?></p>
</div>