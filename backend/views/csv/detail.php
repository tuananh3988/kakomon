<?php

use yii\helpers\Url;
use yii\helpers\Html;
use common\models\Exam;

Yii::$app->view->title = 'Detail Upload Csv';
$logContent = json_decode($logCsvItem->content_log);
?>
<div>
    <div class="page-title">
        <div class="title_left">
            <h3>Detail Upload Csv</h3>
        </div>
    </div>
    <div class="clearfix"></div>

    <div class="row">
        <div class="col-md-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Basic Information Upload Csv</h2>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content basic-info">
                    <br>
                    <?php if ($logContent->errorCode == 1) : ?>
                    <div class="form-group row">
                        <label class="control-label col-xs-12" style="color: red"><?= $logContent->message?></label>
                    </div>
                    <?php else : ?>
                    <div class="form-group row">
                        <label class="control-label col-xs-12" style="color: red">Number of errors:<?= count($logContent->data)?></label>
                        <?php if (count($logContent->data) > 0) : ?>
                        <?php foreach ($logContent->data as $value) : ?>
                        <label class="control-label col-xs-12">Line : <span style="color: red"><?= $value;?></span></label>
                        <?php endforeach;?>
                        <?php endif;?>
                    </div>
                    <?php endif;?>
                </div>
            </div>
        </div>
    </div>
</div>