<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use common\models\Quiz;
//use kartik\file\FileInput;

$this->title = 'Add Question!';
?>

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
                            <?= $form->field($question, 'category_id_2', ['options' => ['class' => ''], 'template' => '{input}{error}'])->dropDownList([], ['prompt' => 'Select sub1 category', 'class' => 'form-control select-sub1-cat'])->label('') ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Sub-Cat2</label>
                        <div class="col-md-5 col-sm-9 col-xs-12">
                            <?= $form->field($question, 'category_id_3', ['options' => ['class' => ''], 'template' => '{input}{error}'])->dropDownList([], ['prompt' => 'Select sub2 category', 'class' => 'form-control select-sub2-cat'])->label('') ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Sub-Cat3</label>
                        <div class="col-md-5 col-sm-9 col-xs-12">
                            <?= $form->field($question, 'category_id_4', ['options' => ['class' => ''], 'template' => '{input}{error}'])->dropDownList([], ['prompt' => 'Select sub3 category', 'class' => 'form-control'])->label('') ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Content Question <span class="required">*</span></label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                            <?= $form->field($question, 'question')->textarea(['class' => 'form-control','rows' => '4'])->label(false) ?>
                        </div>
                        
                    </div>
                    
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Question Img</label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                            <?= $form->field($question, 'question_img')->fileInput()->label(false) ?>
                        </div>
                        
                    </div>

                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Answer 1</label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                            <?= $form->field($answer['answer1'], '[answer1]content')->textarea(['class' => 'form-control','rows' => '2'])->label(false) ?>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Answer 1 Img</label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                            <?= $form->field($answer['answer1'], '[answer1]answer_img')->fileInput()->label(false) ?>
                        </div>
                        
                    </div>
                    
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Answer 2</label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                            <?= $form->field($answer['answer2'], '[answer2]content')->textarea(['class' => 'form-control','rows' => '2'])->label(false) ?>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Answer 2 Img</label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                            <?= $form->field($answer['answer2'], '[answer2]answer_img')->fileInput()->label(false) ?>
                        </div>
                        
                    </div>
                    
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Answer 3</label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                            <?= $form->field($answer['answer3'], '[answer3]content')->textarea(['class' => 'form-control','rows' => '2'])->label(false) ?>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Answer 3 Img</label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                            <?= $form->field($answer['answer3'], '[answer3]answer_img')->fileInput()->label(false) ?>
                        </div>
                        
                    </div>
                    
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Answer 4</label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                            <?= $form->field($answer['answer4'], '[answer4]content')->textarea(['class' => 'form-control','rows' => '2'])->label(false) ?>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Answer 4 Img</label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                            <?= $form->field($answer['answer4'], '[answer4]answer_img')->fileInput()->label(false) ?>
                        </div>
                        
                    </div>
                    
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Answer 5</label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                            <?= $form->field($answer['answer5'], '[answer5]content')->textarea(['class' => 'form-control','rows' => '2'])->label(false) ?>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Answer 5 Img</label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                            <?= $form->field($answer['answer5'], '[answer5]answer_img')->fileInput()->label(false) ?>
                        </div>
                        
                    </div>
                    
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Answer 6</label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                            <?= $form->field($answer['answer6'], '[answer6]content')->textarea(['class' => 'form-control','rows' => '2'])->label(false) ?>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Answer 6 Img</label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                            <?= $form->field($answer['answer6'], '[answer6]answer_img')->fileInput()->label(false) ?>
                        </div>
                        
                    </div>
                    
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Answer 7</label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                            <?= $form->field($answer['answer7'], '[answer7]content')->textarea(['class' => 'form-control','rows' => '2'])->label(false) ?>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Answer 7 Img</label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                            <?= $form->field($answer['answer7'], '[answer7]answer_img')->fileInput()->label(false) ?>
                        </div>
                        
                    </div>
                    
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Answer 8</label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                            <?= $form->field($answer['answer8'], '[answer8]content')->textarea(['class' => 'form-control','rows' => '2'])->label(false) ?>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Answer 8 Img</label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                            <?= $form->field($answer['answer8'], '[answer8]answer_img')->fileInput()->label(false) ?>
                        </div>
                        
                    </div>
                    
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