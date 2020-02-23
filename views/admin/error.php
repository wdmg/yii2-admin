<?php

use yii\helpers\Html;
use yii\web\View;
use yii\web\HttpException;

?>
<div class="page-header">
    <h1>
        <?php
            if (isset($statuses[$code])) {
                $this->title = $code ." " . $statuses[$code];
            } else {
                $this->title = $code;
            }
        ?>
        <?= $this->title ?> <small class="text-muted pull-right">[v.<?= $this->context->module->version ?>]</small>
    </h1>
</div>
<div class="admin-error">
    <div class="alert alert-<?= $type ?>">
        <?= $message ?>
    </div>
</div>

<?php echo $this->render('../_debug'); ?>
