<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\components\Utility;
use common\models\Category;
use common\models\Answer;

Yii::$app->view->title = 'Detail Question';

?>
<link rel="stylesheet" href="<?= Yii::$app->request->baseUrl; ?>/css/colorbox.css" />
<script src="<?= Yii::$app->request->baseUrl; ?>/js/jquery.colorbox.js"></script>
<div>
    <div class="page-title">
        <div class="title_left">
            <h3>Detail Question</h3>
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="row">
        <div class="col-md-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Basic Information Question</h2>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content basic-info">
                    <br>
                    <div class="form-group row">
                        <label class="control-label col-md-2 col-sm-3 col-xs-12">Question :</label>
                        <div class="col-md-10 col-sm-9 col-xs-12">
                            <label class="control-label col-md-12 col-sm-3 col-xs-12 text-left"><?= $quizItem->question ?></label>
                        </div>
                    </div>
                    <?php $img = Utility::getImage('question', $quizItem->quiz_id);?>
                    <?php if ($img) : ?>
                        <div class="form-group row detail-img" id="img-question">
                            <label class="control-label col-md-2 col-sm-3 col-xs-12">Img Question :</label>
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
                    <div class="form-group row">
                        <label class="control-label col-md-2 col-sm-3 col-xs-12">Year :</label>
                        <div class="col-md-10 col-sm-9 col-xs-12">
                            <label class="control-label col-md-12 col-sm-3 col-xs-12 text-left"><?= $quizItem->quiz_year;?></label>
                        </div>
                    </div>
                    
                    <div class="form-group row">
                        <label class="control-label col-md-2 col-sm-3 col-xs-12">Number :</label>
                        <div class="col-md-10 col-sm-9 col-xs-12">
                            <label class="control-label col-md-12 col-sm-3 col-xs-12 text-left"><?= $quizItem->quiz_number;?></label>
                        </div>
                    </div>
                    
                    <div class="form-group row">
                        <label class="control-label col-md-2 col-sm-3 col-xs-12">Test Times :</label>
                        <div class="col-md-10 col-sm-9 col-xs-12">
                            <label class="control-label col-md-12 col-sm-3 col-xs-12 text-left"><?= $quizItem->test_times;?></label>
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
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="form-group">
                <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-2">
                    <a href="<?= Url::to(['/question/index']); ?>" class="btn btn-success"><i class="fa fa-reply"></i>&nbsp;&nbsp;Back</a>
                    <a href="<?= Url::to(['/question/save', 'quizId' => $quizItem->quiz_id]); ?>" class="btn btn-success"><i class="fa fa-pencil-square-o"></i>&nbsp;&nbsp;Edit</a>
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