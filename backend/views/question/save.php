<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use common\models\Quiz;
use common\models\Category;
use common\components\Utility;

$this->title = 'Add Question!';
$subCat1 = [];
$subCat2 = [];
$subCat3 = [];
if ($question->category_id_1) {
    $subCat1 = Category::getsubcategory($question->category_id_1);
}
if ($question->category_id_2) {
    $subCat2 = Category::getsubcategory($question->category_id_2);
}
if ($question->category_id_3) {
    $subCat3 = Category::getsubcategory($question->category_id_3);
}
?>
<link rel="stylesheet" href="<?= Yii::$app->request->baseUrl; ?>/css/colorbox.css" />
<script src="<?= Yii::$app->request->baseUrl; ?>/js/jquery.colorbox.js"></script>
<div class="">
    <div class="page-title">
        <div class="title_left">
            <h3>Form Add Question</h3>
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_content">
                    <?php $form = ActiveForm::begin(['options' => ['class' => 'form-horizontal form-label-left', 'role' => 'form']]); ?>
                    <div class="form-group"> 
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Category</label>
                        <div class="col-md-5 col-sm-9 col-xs-12">
                            <?= $form->field($question, 'category_id_1', ['options' => ['class' => ''], 'template' => '{input}{error}'])->dropDownList($rootCat, ['prompt' => 'Select category', 'class' => 'form-control select-root-cat'])->label('') ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Sub-Cat1</label>
                        <div class="col-md-5 col-sm-9 col-xs-12">
                            <?= $form->field($question, 'category_id_2', ['options' => ['class' => ''], 'template' => '{input}{error}'])->dropDownList($subCat1, ['prompt' => 'Select sub1 category', 'class' => 'form-control select-sub1-cat'])->label('') ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Sub-Cat2</label>
                        <div class="col-md-5 col-sm-9 col-xs-12">
                            <?= $form->field($question, 'category_id_3', ['options' => ['class' => ''], 'template' => '{input}{error}'])->dropDownList($subCat2, ['prompt' => 'Select sub2 category', 'class' => 'form-control select-sub2-cat'])->label('') ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Sub-Cat3</label>
                        <div class="col-md-5 col-sm-9 col-xs-12">
                            <?= $form->field($question, 'category_id_4', ['options' => ['class' => ''], 'template' => '{input}{error}'])->dropDownList($subCat3, ['prompt' => 'Select sub3 category', 'class' => 'form-control'])->label('') ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Content Question <span class="required">*</span></label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                            <?= $form->field($question, 'question')->textarea(['class' => 'form-control','rows' => '4'])->label(false) ?>
                        </div>
                        
                    </div>
                    
                    <?php if ($flag == 1) : ?>
                    <?php $img = Utility::getImage('question', $question->quiz_id);?>
                        <?php if ($img) : ?>
                            <div class="form-group detail-img" id="img-question">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12">&nbsp;</label>
                                <div class="col-md-9 col-sm-9 col-xs-12 img-answer">
                                    <a href="<?= $img ?>" class="group1"><img src="<?= $img ?>" class="avatar" style="max-width: 100px;"/></a><br/>
                                    <a href="javascript:void(0)" class="remove" onclick="removeImgQuestion()"><i class="glyphicon glyphicon-trash"></i>&nbsp;</a>
                                </div>
                            </div>
                        <?php endif;?>
                    <?= $form->field($question, 'remove_img_question_flg')->hiddenInput()->label(false);?>
                    <?php endif;?>
                    
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Question Img</label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                            <?= $form->field($question, 'question_img')->fileInput()->label(false) ?>
                        </div>
                    </div>

                    <?php for ($i= 1; $i <= 8; $i++) : ?>
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Answer <?= $i;?></label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                            <?= $form->field($answer['answer'.$i], '[answer'.$i.']content')->textarea(['class' => 'form-control','rows' => '2'])->label(false) ?>
                        </div>
                    </div>
                    
                    <?php if ($flag == 1) : ?>
                    <?php $img = Utility::getImage('answer', $question->quiz_id, $i);?>
                        <?php if ($img) : ?>
                        <div class="form-group detail-img" id="img-ans-<?=$i?>">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">&nbsp;</label>
                            <div class="col-md-9 col-sm-9 col-xs-12 img-answer">
                                <a href="<?= $img ?>" class="group1"><img src="<?= $img ?>" class="avatar" style="max-width: 100px;"/></a>
                                <a href="javascript:void(0)" class="remove" onclick="removeImgAns(<?=$i?>)"><i class="glyphicon glyphicon-trash"></i>&nbsp;</a>
                            </div>
                        </div>
                        <?php endif;?>
                    <?php endif;?>
                    <?= $form->field($answer['answer'.$i], '[answer'.$i.']remove_img_flg')->hiddenInput()->label(false);?>
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Answer <?= $i; ?> Img</label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                            <?= $form->field($answer['answer'.$i], '[answer'.$i.']answer_img')->fileInput()->label(false) ?>
                        </div>
                    </div>
                    
                    <?php endfor;?>
                    
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Correct Answer<span class="required">*</span></label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                            <?=
                                $form->field($question, 'answer_id')
                                    ->radioList(
                                        [1 => 'Answer 1:', 2 => 'Answer 2:', 3 => 'Answer 3:', 4 => 'Answer 4:', 5 => 'Answer 5:', 6 => 'Answer 6:', 7 => 'Answer 7:', 8 => 'Answer 8:'],
                                        [
                                            'item' => function($index, $label, $name, $checked, $value) {
                                                $return = $label;
                                                if ($checked) {
                                                    $return .= '<input type="radio" class="flat" name="' . $name . '" value="' . $value . '" tabindex="3" checked="">';
                                                } else {
                                                    $return .= '<input type="radio" class="flat" name="' . $name . '" value="' . $value . '" tabindex="3">';
                                                }
                                                
                                                return $return;
                                            }
                                        ]
                                    )
                                ->label(false);
                            ?>
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