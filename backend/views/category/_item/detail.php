<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use common\models\Category;
?>
<div class="col-sm-8" id="detail-category">
    <div id="w0-detail" class="kv-detail-container">
        <div class="kv-detail-heading">
            <div class="pull-right">
                <button type="button" class="btn btn-default tooltip-f" id="add-sub-cat" title="add-sub-category" <?php if($firstCategory->cateory_id == NULL || $firstCategory->level == 4) echo 'disabled="disabled"'?>>
                    <i class="fa fa-plus"></i>
                </button>
                <button type="button" class="btn btn-default tooltip-f" id="add-cat" title="add-category">
                    <i class="fa fa-tree"></i>
                </button>
                <button type="button" class="btn btn-default tooltip-f" id="delete-cat" title="delete-category" <?php if($firstCategory->cateory_id == NULL || !Category::checkQuizWithCategory($firstCategory->cateory_id)) echo 'disabled="disabled"'?>>
                    <i class="fa fa-trash"></i>
                </button>
            </div>
            <div class="kv-detail-crumbs"><?= $breadCrumbs;?></div>
            <div class="clearfix"></div>
        </div>
        <?php if($firstCategory) :?>
            <?php $form = ActiveForm::begin(['options' => ['class' => 'form-horizontal form-label-left', 'role' => 'form', 'id' => 'form']]); ?>
            <?= $form->field($firstCategory, 'flagDelete', ['options' => ['class' => ''], 'template' => '{input}{error}'])->hiddenInput(['id' => 'flag-delete'])->label(false);?>
            <?= $form->field($firstCategory, 'idParent', ['options' => ['class' => ''], 'template' => '{input}{error}'])->hiddenInput(['id' => 'id-parent'])->label(false);?>
            <?= $form->field($firstCategory, 'level', ['options' => ['class' => ''], 'template' => '{input}{error}'])->hiddenInput(['id' => 'level'])->label(false);?>
            <?= $form->field($firstCategory, 'type', ['options' => ['class' => ''], 'template' => '{input}{error}'])->hiddenInput(['id' => 'type'])->label(false);?>
            <div class="kv-treeview-alerts">
                <?php if(Yii::$app->session->hasFlash('sucess')): ?>
                    <div class="alert alert-success"><div>
                        <?= Yii::$app->session->getFlash('sucess') ?>
                    </div></div>
                <?php endif; ?>
                <div class="alert alert-danger hide"><div></div></div>
                <div class="alert alert-warning hide"><div></div></div>
                <div class="alert alert-info <?= ($firstCategory->cateory_id == NULL) ? '' : 'hide'?>"><div><?= ($firstCategory->cateory_id == NULL) ? 'Currently there is no category that you please create a new category.' : ''; ?></div></div>
            </div>
            <?php if ($firstCategory->cateory_id == NULL) $firstCategory->cateory_id = '(New)';?>
            <div class="row">
                <div class="col-sm-4">
                    <div class="form-group field-category-id">
                        <label class="control-label" for="category-id">ID</label>
                        <?= $form->field($firstCategory, 'cateory_id', ['options' => ['class' => ''], 'template' => '{input}{error}'])->textInput(['autofocus' => false, 'class' => 'form-control', 'id' => 'id-cat', 'readonly' => ''])->label(''); ?>
                    </div>
                </div>
                <div class="col-sm-8">
                    <div class="form-group field-category-name required">
                        <label class="control-label" for="category-name">Name</label>
                        <?= $form->field($firstCategory, 'name', ['options' => ['class' => ''], 'template' => '{input}{error}'])->textInput(['autofocus' => false, 'class' => 'form-control', 'id' => 'name'])->label(''); ?>
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