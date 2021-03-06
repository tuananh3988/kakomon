<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use common\models\Quiz;
use common\models\Category;
use common\components\Utility;
use dosamigos\tinymce\TinyMce;
use kartik\file\FileInput;

$this->title = 'Add Quick Question!';
if ($flag == 1) {
    $this->title = 'Edit Quick Question!';
}
?>
<link rel="stylesheet" href="<?= Yii::$app->request->baseUrl; ?>/css/colorbox.css" />
<script src="<?= Yii::$app->request->baseUrl; ?>/js/jquery.colorbox.js"></script>
<div class="">
    <div class="page-title">
        <div class="title_left">
            <?php if ($flag == 0) :?>
            <h3>Form Add Quick Question</h3>
            <?php else : ?>
            <h3>Form Edit Quick Question</h3>
            <?php endif;?>
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_content">
                    <?php $form = ActiveForm::begin(['options' => ['class' => 'form-horizontal form-label-left', 'role' => 'form']]); ?>
                    <?= $form->field($question, 'file')->fileInput(['style' => 'display: none'])->label(''); ?>
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Content Question <span class="required">*</span></label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                            <?= $form->field($question, 'question')->widget(TinyMce::className(), [
                                'options' => ['rows' => 6],
                                'language' => 'en_CA',
                                'clientOptions' => [
                                    'plugins' => [
                                        "advlist autolink lists link charmap print preview anchor",
                                        "searchreplace visualblocks code fullscreen",
                                        "insertdatetime table contextmenu paste"
                                    ],
                                    'toolbar' => "undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image"
                                ]
                            ])->label('');?>
                            
                        </div>
                        
                    </div>
                    
                    <?php $img = '';?>
                    <?php if ($flag == 1) : ?>
                    <?php $img = Utility::getImage('question', $question->quiz_id, null, true);?>
                    <?php endif;?>
                    
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Question Img</label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                            <?= $form->field($question, 'question_img')->widget(FileInput::classname(), ['options' => ['accept' => 'image/*'],
                                'pluginOptions' => [
                                        'showCaption' => true,
                                        'showRemove' => true,
                                        'showUpload' => false,
                                        'browseClass' => 'btn btn-primary btn-file',
                                        'browseIcon' => '<i class="glyphicon glyphicon-camera"></i> ',
                                        //'allowedFileExtensions' => ['jpg','gif','png'],
                                        'overwriteInitial' => true,
                                        'initialPreview' => [
                                            ($img) ? Html::img("/".$img, ['class'=>'file-preview-image', 'alt'=>'', 'title'=>'']) : null
                                        ],
                                        'initialPreviewConfig' => [
                                            ['caption' => ($img) ? basename("/".$img).PHP_EOL : NULL],
                                        ],
                                        'initialCaption' => ($img) ? basename("/".$img).PHP_EOL : NULL,
                                        
                                        'pluginEvents' => [
                                            "fileclear" => "function() {console.log('212121')}",
                                            "filereset" => "function() {console.log('12dsds')}",
                                        ]
                                    ]])->label(FALSE) ?>
                        </div>
                        <?= $form->field($question, 'remove_img_question_flg')->hiddenInput()->label(false);?>
                    </div>

                    <?php for ($i= 1; $i <= 8; $i++) : ?>
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Answer <?= $i;?></label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                            <?= $form->field($answer['answer'.$i], '[answer'.$i.']content')->widget(TinyMce::className(), [
                                'options' => ['rows' => 2],
                                'language' => 'en_CA',
                                'clientOptions' => [
                                    'plugins' => [
                                        "advlist autolink lists link charmap print preview anchor",
                                        "searchreplace visualblocks code fullscreen",
                                        "insertdatetime table contextmenu paste"
                                    ],
                                    'toolbar' => "undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image"
                                ]
                            ])->label('');?>
                        </div>
                    </div>
                    
                    <?php $img = '';?>
                    <?php if ($flag == 1) : ?>
                    <?php $img = Utility::getImage('answer', $question->quiz_id, $i, true);?>
                    <?php endif;?>
                    
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Answer <?= $i; ?> Img</label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                            <?= $form->field($answer['answer'.$i], '[answer'.$i.']answer_img')->widget(FileInput::classname(), ['options' => ['accept' => 'image/*'],
                                'pluginOptions' => [
                                        'showCaption' => true,
                                        'showRemove' => true,
                                        'showUpload' => false,
                                        'browseClass' => 'btn btn-primary btn-file ans-images',
                                        'browseIcon' => '<i class="glyphicon glyphicon-camera"></i> ',
                                        //'allowedFileExtensions' => ['jpg','jpeg','png'],
                                        'overwriteInitial' => true,
                                        'initialPreview' => [
                                            ($img) ? Html::img("/".$img, ['class'=>'file-preview-image', 'alt'=> '', 'title'=> '']) : null
                                        ],
                                        'initialPreviewConfig' => [
                                            ['caption' => ($img) ? basename("/".$img).PHP_EOL : NULL],
                                        ],
                                        'initialCaption' => ($img) ? basename("/".$img).PHP_EOL : NULL,
                                    ]])->label(FALSE) ?>
                        </div>
                        <?= $form->field($answer['answer'.$i], '[answer'.$i.']remove_img_flg')->hiddenInput()->label(false);?>
                    </div>
                    
                    <?php endfor;?>
                    
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Correct Answer<span class="required">*</span></label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                            <?php for ($i= 1; $i <= 8; $i++) : ?>
                                <?= $form->field($question, 'quiz_answer'.$i, ['options' => ['class' => 'custom-checkbox'], 'template' => '{input}'])->checkbox(['class' => 'flat','label'=>'Answer '. $i]); ?>
                            <?php endfor;?>
                        </div>
                        <?php if (Yii::$app->session->hasFlash('validate_answer')): ?>
                            <div class="help-block show-error">
                                <?= Yii::$app->session->getFlash('validate_answer') ?>
                            </div>
                        <?php endif; ?>
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
    jQuery(function ($) {
        $(document).ready(function(){
            $(".group1").colorbox({
                'photo':true, width:"50%",
                onOpen:function(){ $('body').addClass('scroll'); },
                onClosed:function(){ $('body').removeClass('scroll'); }
            });
        });
    });
</script>