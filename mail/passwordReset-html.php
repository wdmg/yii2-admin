<?php

use yii\helpers\Html;
use yii\helpers\Url;
use wdmg\admin\AdminAsset;

/* @var $this yii\web\View */
/* @var $user \wdmg\users\models\Users */

use wdmg\admin\AdminAsset;

$bundle = AdminAsset::register($this);
$bundle->js = null;
$bundle->css = null;

if (isset(Yii::$app->mails))
    $logotypeLink = Yii::$app->mails->getTrackingUrl($bundle->baseUrl . '/images/logotype.png');
else
    $logotypeLink = $bundle->baseUrl . '/images/logotype.png';

$resetLink = Yii::$app->urlManager->createAbsoluteUrl([$linkRoute, 'token' => $user->password_reset_token]);

if (isset(Yii::$app->params["mailer.webMailUrl"]))
    $webMailUrl = Url::to(Yii::$app->params["mailer.webMailUrl"]);

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
    <hr/>
<?php
if (isset(Yii::$app->mails)) {
    if ($webversion_url = Yii::$app->mails->getWebversionUrl()) {
        echo Yii::t('app/modules/admin', 'Do not see the images? Go to the {link} of this email.', [
            'link' => Html::a('web-version', $webversion_url),
        ]);
    }
}
?>