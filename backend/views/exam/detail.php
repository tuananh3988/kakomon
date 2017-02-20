<?php

use yii\helpers\Url;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\components\Utility;
use common\models\Exam;
use yii\widgets\Pjax;

Yii::$app->view->title = 'Detail Exam';

?>
<div>
    <div class="page-title">
        <div class="title_left">
            <h3>Detail Exam</h3>
        </div>
    </div>
    <div class="clearfix"></div>
    <?php if (Yii::$app->session->hasFlash('sucess_exam')): ?>
        <div class="alert alert-success alert-dismissible fade in" role="alert">
            <?= Yii::$app->session->getFlash('sucess_exam') ?>
        </div>
    <?php endif; ?>

    <div class="row">
        <?php $form = ActiveForm::begin(['options' => ['class' => '', 'role' => 'form']]); ?>
        <div class="col-md-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Basic Information Exam</h2>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content basic-info">
                    <br>
                    <div class="form-group row">
                        <label class="control-label col-md-2 col-sm-3 col-xs-12">Name:</label>
                        <div class="col-md-10 col-sm-9 col-xs-12">
                            <label class="control-label col-md-12 col-sm-3 col-xs-12 text-left"><?= $examItem->name ?></label>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="control-label col-md-2 col-sm-3 col-xs-12">Status</label>
                        <div class="col-md-10 col-sm-9 col-xs-12">
                            <label class="control-label col-md-12 col-sm-3 col-xs-12 text-left"><?= (array_key_exists($examItem->status, Exam::$STATUS)) ?  Exam::$STATUS[$examItem->status] : ''; ?></label>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="control-label col-md-2 col-sm-3 col-xs-12">Total Quiz</label>
                        <div class="col-md-10 col-sm-9 col-xs-12">
                            <label class="control-label col-md-12 col-sm-3 col-xs-12 text-left"><?= $examItem->total_quiz ?></label>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="control-label col-md-2 col-sm-3 col-xs-12">Start Date</label>
                        <div class="col-md-10 col-sm-9 col-xs-12">
                            <label class="control-label col-md-12 col-sm-3 col-xs-12 text-left"><?= $examItem->start_date ?></label>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="control-label col-md-2 col-sm-3 col-xs-12">End Date</label>
                        <div class="col-md-10 col-sm-9 col-xs-12">
                            <label class="control-label col-md-12 col-sm-3 col-xs-12 text-left"><?= $examItem->end_date ?></label>
                        </div>
                    </div>
                    
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <div class="form-group">
                            <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-2">
                                <a href="<?= Url::to(['/exam/index']); ?>" class="btn btn-success"><i class="fa fa-reply"></i>&nbsp;&nbsp;Back</a>
                                <?php if ($totalQuiz == $examItem->total_quiz) : ?>
                                <?= Html::submitButton('<i class="fa fa-pencil-square-o"></i>&nbsp;&nbsp;Start Exam', ['class' => 'btn btn-success', 'name' => 'Start Exam']) ?>
                                <?php endif;?>
                            </div>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
    
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>List Question</h2>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <div id="datatable_wrapper" class="table-responsive">
                        <?php if ($dataProvider->getTotalCount() == 0) : ?>
                            <p class="txtWarning"><span class="iconNo">Data does not exist</span></p>
                        <?php else : ?>
                            <?php Pjax::begin(); ?>
                            <?=
                            GridView::widget([
                                'dataProvider' => $dataProvider,
                                'layout' => '<div class="mBoxitem_listinfo">{summary}</div>{items}<div class="mBoxitem_listinfo">'
                                . '<div id="paging" class="light-theme simple-pagination">{pager}</div></div>',
                                'summary' => '<div class="pageList_data"><strong>ALL {totalCount} Item {begin} ～ {end}</strong>'
                                . '</div><div class="pageList_del"><div class="pageList_del_item"></div></div>',
                                'columns' => [
                                    [
                                        'attribute' => 'exam_quiz_id',
                                        'label' => 'Question ID',
                                        'headerOptions' => ['class' => 'icon-sort'],
                                        'content' => function ($data) {
                                            return '<a data-pjax="0" href="' . Url::to(['/question/detail',
                                                        'quizId' => $data["quiz_id"]]) . '">' . $data['quiz_id'] . '</a>';
                                        }
                                    ],
                                    [
                                        'attribute' => 'question',
                                        'label' => 'Question',
                                        'headerOptions' => ['class' => ''],
                                        'content' => function ($data) {
                                            return $data['question'];
                                        }
                                    ],
                                    [
                                        'attribute' => 'created_date',
                                        'label' => 'Create Date',
                                        'headerOptions' => ['class' => 'icon-sort'],
                                        'content' => function ($data) {
                                            if (!empty($data['created_date']) && $data['created_date'] != '0000-00-00 00:00:00') {
                                                return $data['created_date'];
                                            } else {
                                                return '';
                                            }
                                        }
                                    ]
                                ],
                                'tableOptions' => ['class' => 'table table-striped table-bordered'],
                                'pager' => [
                                    'prevPageLabel' => 'Prev',
                                    'nextPageLabel' => 'Next',
                                    'activePageCssClass' => 'paginate_button active',
                                    'disabledPageCssClass' => 'paginate_button previous disabled',
                                    'options' => [
                                        'class' => 'pagination',
                                        'id' => 'pager-container',
                                    ],
                                ],
                            ]);
                            ?>
                        <?php Pjax::end(); ?>
                    <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>