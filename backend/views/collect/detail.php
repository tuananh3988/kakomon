<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\components\Utility;
use common\models\Category;
use common\models\Answer;

Yii::$app->view->title = 'Detail Question Collect';

?>
<link rel="stylesheet" href="<?= Yii::$app->request->baseUrl; ?>/css/colorbox.css" />
<script src="<?= Yii::$app->request->baseUrl; ?>/js/jquery.colorbox.js"></script>
<div>
    <?php $form = ActiveForm::begin(['options' => ['class' => '', 'role' => 'form']]); ?>
    <div class="page-title">
        <div class="title_left">
            <h3>Detail Question Collect</h3>
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="row">
        <div class="col-md-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Basic Information Question Collect</h2>
                    <ul class="nav navbar-right panel_toolbox custom_toolbox">
                        <?= $form->field($modelExamQuiz, 'exam_id', ['options' => ['class' => ''], 'template' => '{input}{error}'])->dropDownList($listExam, ['prompt' => 'Select Exam', 'class' => 'form-control select-sub1-cat'])->label('') ?>
                        <?= Html::submitButton('Add Exam', ['class' => 'btn btn-success', 'name' => 'button_create']) ?>
                    </ul>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content basic-info">
                    <br>
                    <div class="form-group row">
                        <label class="control-label col-md-2 col-sm-3 col-xs-12">Question :</label>
                        <div class="col-md-10 col-sm-9 col-xs-12">
                            <label class="control-label col-md-12 col-sm-3 col-xs-12"><?= $quizItem->question ?></label>
                        </div>
                    </div>
                    <?php $img = Utility::getImage('question', $quizItem->quiz_id);?>
                    <?php if ($img) : ?>
                        <div class="form-group row detail-img" id="img-question">
                            <label class="control-label col-md-2 col-sm-3 col-xs-12">Img Question</label>
                            <div class="col-md-10 col-sm-9 col-xs-12 img-answer">
                                <a href="<?= $img ?>" class="group1"><img src="<?= $img ?>" class="avatar" style="max-width: 100px;"/></a><br/>
                            </div>
                        </div>
                    <?php endif;?>
                    
                    <div class="form-group row">
                        <label class="control-label col-md-2 col-sm-3 col-xs-12">Category root :</label>
                        <div class="col-md-10 col-sm-9 col-xs-12">
                            <label class="control-label col-md-12 col-sm-3 col-xs-12 text-left"><?= Category::getDetailNameCategory($quizItem->category_main_id); ?></label>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="control-label col-md-2 col-sm-3 col-xs-12">Sub1 category :</label>
                        <div class="col-md-10 col-sm-9 col-xs-12">
                            <label class="control-label col-md-12 col-sm-3 col-xs-12 text-left"><?= Category::getDetailNameCategory($quizItem->category_a_id);?></label>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="control-label col-md-2 col-sm-3 col-xs-12">Sub2 category :</label>
                        <div class="col-md-10 col-sm-9 col-xs-12">
                            <label class="control-label col-md-12 col-sm-3 col-xs-12 text-left"><?= Category::getDetailNameCategory($quizItem->category_b_id);?></label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Information Answer</h2>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content custom_x_content">
                    <?php for($i = 1; $i <=4; $i++):?>
                    <?php
                        $answer = Answer::find()->where(['quiz_id' => $quizItem->quiz_id, 'order' => $i])->one();
                        $img = Utility::getImage('answer', $quizItem->quiz_id, $i);
                    ?>
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Answer <?= $i?> : </label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                            <?= ($answer) ? $answer->content : '';?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Img Answer <?= $i?> : </label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                            <?php if($img) :?>
                            <a href="<?= $img ?>" class="group1"><img src="<?= $img ?>" class="avatar" style="max-width: 100px;"/></a>
                            <?php else : ?>
                            <img src="<?= Yii::$app->request->baseUrl; ?>/images/no-image.png" class="avatar" style="max-width: 100px;" width="100px" height="100px"/>
                            <?php endif;?>
                        </div>
                    </div>
                    <?php endfor;?>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Information Answer</h2>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content custom_x_content">
                    <?php for($i = 5; $i <=8; $i++):?>
                    <?php
                        $answer = Answer::find()->where(['quiz_id' => $quizItem->quiz_id, 'order' => $i])->one();
                        $img = Utility::getImage('answer', $quizItem->quiz_id, $i);
                    ?>
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Answer <?= $i?> :</label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                            <?= ($answer) ? $answer->content : '';?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Img Answer <?= $i?> :</label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                            <?php if($img) :?>
                            <a href="<?= $img ?>" class="group1"><img src="<?= $img ?>" class="avatar" style="max-width: 100px;"/></a>
                            <?php else : ?>
                            <img src="<?= Yii::$app->request->baseUrl; ?>/images/no-image.png" class="avatar" style="max-width: 100px;"/>
                            <?php endif;?>
                        </div>
                    </div>
                    <?php endfor;?>
                </div>
            </div>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
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