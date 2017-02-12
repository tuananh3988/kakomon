<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

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
                            <?= $form->field($question, 'id_cat_root', ['options' => ['class' => ''], 'template' => '{input}{error}'])->dropDownList($rootCat, ['prompt' => 'Select category', 'class' => 'form-control select-root-cat'])->label('') ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Sub-Cat1</label>
                        <div class="col-md-5 col-sm-9 col-xs-12">
                            <?= $form->field($question, 'id_sub1', ['options' => ['class' => ''], 'template' => '{input}{error}'])->dropDownList([], ['prompt' => 'Select sub1 category', 'class' => 'form-control select-sub1-cat'])->label('') ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Sub-Cat2</label>
                        <div class="col-md-5 col-sm-9 col-xs-12">
                            <?= $form->field($question, 'id_sub2', ['options' => ['class' => ''], 'template' => '{input}{error}'])->dropDownList([], ['prompt' => 'Select sub2 category', 'class' => 'form-control select-sub2-cat'])->label('') ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Sub-Cat3</label>
                        <div class="col-md-5 col-sm-9 col-xs-12">
                            <?= $form->field($question, 'id_sub3', ['options' => ['class' => ''], 'template' => '{input}{error}'])->dropDownList([], ['prompt' => 'Select sub3 category', 'class' => 'form-control'])->label('') ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Content Question <span class="required">*</span></label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                            <?= $form->field($question, 'content_question')->textarea(['class' => 'form-control','rows' => '4'])->label(false) ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Answer A<span class="required">*</span></label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                            <?= $form->field($question, 'answer_1')->textarea(['class' => 'form-control','rows' => '2'])->label(false) ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Answer B<span class="required">*</span></label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                            <?= $form->field($question, 'answer_2')->textarea(['class' => 'form-control','rows' => '2'])->label(false) ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Answer C<span class="required">*</span></label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                            <?= $form->field($question, 'answer_3')->textarea(['class' => 'form-control','rows' => '2'])->label(false) ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Answer D<span class="required">*</span></label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                            <?= $form->field($question, 'answer_4')->textarea(['class' => 'form-control','rows' => '2'])->label(false) ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Correct Answer<span class="required">*</span></label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                            <?=
                                $form->field($question, 'correct_answer')
                                    ->radioList(
                                        [1 => 'Answer A:', 2 => 'Answer B:', 3 => 'Answer C:', 4 => 'Answer D:'],
                                        [
                                            'item' => function($index, $label, $name, $checked, $value) {

                                                $return = $label;
                                                $return .= '<input type="radio" class="flat" name="' . $name . '" value="' . $value . '" tabindex="3">';
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