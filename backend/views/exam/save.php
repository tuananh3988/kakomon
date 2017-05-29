<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use common\components\Utility;
use common\models\Exam;
if ($flag == 0) {
    $title = 'Add Exam';
} else {
    $title = 'Edit Exam';
}
$this->title = $title;
?>
<div class="">
    <div class="page-title">
        <div class="title_left">
            <h3>Form <?= $title?></h3>
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_content">
                    <?php $form = ActiveForm::begin(['options' => ['class' => 'form-horizontal form-label-left', 'role' => 'form']]); ?>
                    <div class="form-group"> 
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Name</label>
                        <div class="col-md-5 col-sm-9 col-xs-12">
                            <?= $form->field($exam, 'name', ['options' => ['class' => ''], 'template' => '{input}{error}'])->textInput(['autofocus' => false, 'class' => 'form-control col-md-7 col-xs-12'])->label(false); ?>
                        </div>
                    </div>
                    <div class="form-group"> 
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Description</label>
                        <div class="col-md-5 col-sm-9 col-xs-12">
                            <?= $form->field($exam, 'exam_desc', ['options' => ['class' => ''], 'template' => '{input}{error}'])->textarea(['autofocus' => false, 'class' => 'form-control col-md-7 col-xs-12', 'rows' => '4'])->label(false); ?>
                        </div>
                    </div>
                    <div class="form-group"> 
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Total Question</label>
                        <div class="col-md-5 col-sm-9 col-xs-12">
                            <?= $form->field($exam, 'total_quiz', ['options' => ['class' => ''], 'template' => '{input}{error}'])->textInput(['autofocus' => false, 'class' => 'form-control col-md-7 col-xs-12'])->label(false); ?>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <div class="controls">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">End Date</label>
                            <div class="col-md-5 col-sm-9 col-xs-12">
                                <?= $form->field($exam, 'end_date', ['options' => ['class' => ''], 'template' => '<div class="xdisplay_inputx form-group has-feedback">{input}{error}<span class="fa fa-calendar-o form-control-feedback left" aria-hidden="true"></span></div>'])->textInput(['autofocus' => false, 'class' => 'form-control col-md-7 col-xs-12 has-feedback-left', 'id' => 'end-time'])->label(false); ?>
                            </div>
                        </div>
                    </div>
                    <div class="ln_solid"></div>

                    <div class="form-group">
                        <div class="col-md-9 col-sm-9 col-xs-12 col-md-offset-3">
                            <?= Html::submitButton('Submit', ['class' => 'btn btn-success', 'name' => 'button_create']) ?>
                        </div>
                    </div>
                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        $('#end-time, #start-time').daterangepicker({
            autoApply: true,
            singleDatePicker: true,
            timePicker: true,
            timePicker24Hour: true,
            format: 'YYYY-MM-DD HH:mm:ss',
            timePickerSeconds: true,
            timePickerIncrement: 1
        }, function(start, end, label) {
          console.log("New date range selected: ' + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD') + ' (predefined range: ' + label + ')");
        });
    });
    
</script>