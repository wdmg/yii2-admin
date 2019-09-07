<?php

use yii\helpers\Html;
use yii\helpers\Url;
use wdmg\admin\AdminAsset;

/* @var $this yii\web\View */
/* @var $user \wdmg\users\models\Users */

$bundle = AdminAsset::register($this);

if (isset(Yii::$app->params["mailer.trackingKey"]))
    $logotypeLink = Url::to(Url::home(true) . 'mail/track?url=' . $bundle->baseUrl . '/images/logotype.png&key=' . Yii::$app->params["mailer.trackingKey"]);
else
    $logotypeLink = Url::to(Url::home(true) . $bundle->baseUrl . '/images/logotype.png');

?>
<div class="password-reset">
    <p style="text-align:center;"><?= Html::a(Html::img($logotypeLink, [
        'style' => "width:160px;"
    ]), Url::home(true)); ?></p>
    <h3><?= Html::encode(Yii::t('app/modules/admin', 'Hi!')); ?></h3>
    <p><b>Name:</b> <?= $name ?></p>
    <p><b>E-mail:</b> <?= $email ?></p>
    <p<b>Message:</b> <?= $message ?></p>
</div>