<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use common\models\Quiz;
use common\models\Category;
use common\components\Utility;

$this->title = 'Import Question!';
?>

<div class="">
    <div class="page-title">
        <div class="title_left">
            <h3>Form Import Question</h3>
        </div>
    </div>
    <div class="clearfix"></div>
    <?php if(Yii::$app->session->hasFlash('sucess_csv')): ?>
        <div class="alert alert-success"><div>
            <?= Yii::$app->session->getFlash('sucess_csv') ?>&nbsp;&nbsp;&nbsp;<a href="<?= Url::to(['/csv/index']); ?>" class="btn btn-primary btn-sm">List Status Upload Csv</a>
        </div></div>
    <?php endif; ?>
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel" style="min-height: 400px;">
                <div class="x_content">
                    <?php $form = ActiveForm::begin(['options' => ['class' => 'form-horizontal form-label-left', 'role' => 'form']]); ?>
                    <div class="form-group"> 
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Import CSV:</label>
                        <div class="col-md-5 col-sm-9 col-xs-12">
                            <?= $form->field($model, 'file')->fileInput()->label(false) ?>
                        </div>
                    </div>
                    
                    <div class="form-group"> 
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Import Images:</label>
                        <div class="col-md-5 col-sm-9 col-xs-12">
                            <?= $form->field($model, 'file_images')->fileInput()->label(false) ?>
                        </div>
                    </div>
                    
                    <div class="ln_solid"></div>

                    <div class="form-group">
                        <div class="col-md-9 col-sm-9 col-xs-12 col-md-offset-3">
                            <?= Html::submitButton('Import', ['class' => 'btn btn-success', 'name' => 'button_create']) ?>
                        </div>
                    </div>
                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>
</div>