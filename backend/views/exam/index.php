<?php

use yii\helpers\Url;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;
use yii\web\Session;
use common\models\Exam;

Yii::$app->view->title = 'List Question';

?>
<div class="page-title">
    <div class="title_left">
        <h3>List Question</h3>
    </div>
</div>
<div class="clearfix"></div>
<?php if (Yii::$app->session->hasFlash('sucess_exam')): ?>
    <div class="alert alert-success alert-dismissible fade in" role="alert">
        <?= Yii::$app->session->getFlash('sucess_exam') ?>
    </div>
<?php endif; ?>

<div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel">
            <div class="x_content">
                <?php
                $form = ActiveForm::begin([
                            'action' => ['exam/index'],
                            'method' => 'get'
                ]);
                ?>
                <form class="form-horizontal form-label-left">
                    <div class="col-md-4 col-sm-12 col-xs-12 form-group">
                        <?= Html::activeTextInput($formSearch, 'name', ['class' => 'form-control col-md-7 col-xs-12', 'placeholder' => 'Name']); ?>
                    </div>
                    <div class="col-md-4 col-sm-12 col-xs-12 form-group">
                        <div class="col-md-11 xdisplay_inputx form-group has-feedback">
                        <?= Html::activeTextInput($formSearch, 'start_date', ['class' => 'form-control has-feedback-left', 'id' => 'start-time', 'placeholder' => '  Start date']); ?>
                            <span class="fa fa-calendar-o form-control-feedback left" aria-hidden="true"></span>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-12 col-xs-12 form-group">
                        <div class="col-md-11 xdisplay_inputx form-group has-feedback">
                        <?= Html::activeTextInput($formSearch, 'end_date', ['class' => 'form-control has-feedback-left', 'id' => 'end-time', 'placeholder' => '  End date']); ?>
                            <span class="fa fa-calendar-o form-control-feedback left" aria-hidden="true"></span>
                        </div>
                    </div>
                    
                    <div class="col-md-4 col-sm-12 col-xs-12 form-group">
                        <?= Html::submitButton('Search', ['class' => 'btn btn-warning']) ?>
                        <a class="btn btn-default" href="<?php echo Url::to(['/exam/index']); ?>">Clear</a>
                    </div>
                </form>
            <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
    
    
    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel">
            <div class="x_content">
                <?php ActiveForm::begin(['options' => ['id' => 'form']]); ?>
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
                                    'attribute' => 'exam_id',
                                    'label' => 'Question ID',
                                    'headerOptions' => ['class' => 'icon-sort'],
                                    'content' => function ($data) {
                                        return '<a data-pjax="0" href="' . Url::to(['/exam/detail',
                                                    'examId' => $data["exam_id"]]) . '">' . $data['exam_id'] . '</a>';
                                    }
                                ],
                                [
                                    'attribute' => 'name',
                                    'label' => 'Name',
                                    'headerOptions' => ['class' => 'icon-sort'],
                                    'content' => function ($data) {
                                        return $data['name'];
                                    }
                                ],
                                [
                                    'attribute' => 'status',
                                    'label' => 'Status',
                                    'headerOptions' => ['class' => 'icon-sort'],
                                    'content' => function ($data) {
                                        if (array_key_exists($data['status'], Exam::$STATUS)) {
                                            return  Exam::$STATUS[$data['status']];
                                        } else {
                                            return $data['status'];
                                        }
                                    }
                                ],
                                [
                                    'attribute' => 'total_quiz',
                                    'label' => 'Total Quiz',
                                    'headerOptions' => ['class' => 'icon-sort'],
                                    'content' => function ($data) {
                                        return $data['total_quiz'];
                                    }
                                ],
                                [
                                    'attribute' => 'start_date',
                                    'label' => 'Start date',
                                    'headerOptions' => ['class' => 'icon-sort'],
                                    'content' => function ($data) {
                                        if (!empty($data['start_date']) && $data['start_date'] != '0000-00-00 00:00:00') {
                                            return $data['start_date'];
                                        } else {
                                            return '';
                                        }
                                    }
                                ],
                                [
                                    'attribute' => 'end_date',
                                    'label' => 'End date',
                                    'headerOptions' => ['class' => 'icon-sort'],
                                    'content' => function ($data) {
                                        if (!empty($data['end_date']) && $data['end_date'] != '0000-00-00 00:00:00') {
                                            return $data['end_date'];
                                        } else {
                                            return '';
                                        }
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
                                ],
                                [
                                    'attribute' => '',
                                    'label' => '#',
                                    'headerOptions' => ['class' => 'icon-sort'],
                                    'content' => function ($data) {
                                return '<div class="action"><a data-pjax="0" href="' . Url::to(['/exam/save',
                                            'examId' => $data["exam_id"]]) . '"><i class="fa fa-edit"></i></a>' . '&nbsp&nbsp&nbsp';
                            }
                                ],
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
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#end-time, #start-time').daterangepicker({
            autoApply: true,
            singleDatePicker: true,
            format: 'YYYY-MM-DD'
        }, function(start, end, label) {
          console.log("New date range selected: ' + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD') + ' (predefined range: ' + label + ')");
        });
    });
    
</script>