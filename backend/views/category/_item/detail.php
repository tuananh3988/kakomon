<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
?>
<div class="col-sm-8" id="detail-category">
    <div id="w0-detail" class="kv-detail-container">
        <div class="kv-detail-heading">
            <div class="pull-right">
                <button type="submit" class="btn btn-default tooltip-f" title="">
                    <i class="fa fa-plus"></i>
                </button>
                <button type="reset" class="btn btn-default tooltip-f" title="">
                    <i class="fa fa-tree"></i>
                </button>
                <button type="submit" class="btn btn-default tooltip-f" title="">
                    <i class="fa fa-trash"></i>
                </button>
            </div>
            <div class="kv-detail-crumbs"><span class="kv-crumb-active"></span></div>
            <div class="clearfix"></div>
        </div>
        <?php if($firstMenu) :?>
            <?php $form = ActiveForm::begin(['options' => ['class' => 'form-horizontal form-label-left', 'role' => 'form']]); ?>
            <div class="kv-treeview-alerts">
                <div class="alert alert-success hide"><div></div></div>
                <div class="alert alert-danger hide"><div></div></div>
                <div class="alert alert-warning hide"><div></div></div>
                <div class="alert alert-info hide"><div></div></div>
            </div>
            <div class="row">
                <div class="col-sm-4">
                    <div class="form-group field-category-id">
                        <label class="control-label" for="category-id">ID</label>
                        <?= Html::activeTextInput($firstMenu, 'cid', ['class' => 'form-control', 'id' => 'cid', 'readonly' => '']); ?>
                    </div>
                </div>
                <div class="col-sm-8">
                    <div class="form-group field-category-name required">
                        <label class="control-label" for="category-name">Name</label>
                        <?= Html::activeTextInput($firstMenu, 'name', ['class' => 'form-control', 'id' => 'name']); ?>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-9 col-sm-9 col-xs-12">
                        <?= Html::submitButton('<i class="glyphicon glyphicon-floppy-disk"></i>', ['class' => 'btn btn-primary tooltip-f', 'title' => 'Save']) ?>
                    </div>
                </div>
            </div>
            <?php ActiveForm::end(); ?>
        <?php else : ?>
            <p>No found category</p>
        <?php endif;?>
        
    </div>
</div>