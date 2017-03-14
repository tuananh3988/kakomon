<?php

use yii\helpers\Url;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;
use yii\web\Session;
use common\models\LogCsv;

Yii::$app->view->title = 'List Log Upload CSV';

?>

<div class="page-title">
    <div class="title_left">
        <h3>List Log Upload CSV</h3>
    </div>
</div>
<div class="clearfix"></div>
<div class="row">
    
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
                            'rowOptions'   => function ($model, $index, $widget, $grid) {
                                return [
                                        'id' => $model['log_id'], 
                                        'onclick' => 'location.href="'
                                            . Yii::$app->urlManager->createUrl('csv/detail') 
                                            . '/"+(this.id);'
                                    ];
                            },
                            'columns' => [
                                [
                                    'attribute' => 'log_id',
                                    'label' => 'Log ID',
                                    'headerOptions' => ['class' => 'icon-sort'],
                                    'content' => function ($data) {
                                        return $data["log_id"];
                                    }
                                ],
                                [
                                    'attribute' => 'file_name',
                                    'label' => 'File Name',
                                    'headerOptions' => ['class' => 'icon-sort'],
                                    'content' => function ($data) {
                                        return $data['file_name'];
                                    }
                                ],
                                [
                                    'attribute' => 'status',
                                    'label' => 'Status',
                                    'headerOptions' => ['class' => 'icon-sort'],
                                    'content' => function ($data) {
                                        if ($data['status'] == LogCsv::STATUS_WAIT) {
                                            return '<a href="javascript:void(0)" class="btn btn-app custom-btn btn-default"><i class="fa fa-pause"></i>Waiting</a>';
                                        } elseif ($data['status'] == LogCsv::STATUS_PROCESS) {
                                            return '<a href="javascript:void(0)" class="btn btn-app custom-btn btn-danger"><i class="fa fa-play"></i>Process</a>';
                                        } else {
                                            return '<a href="'.Url::to(["/csv/index"]).'" class="btn btn-app custom-btn btn-success"><i class="fa fa-check"></i>Done</a>';
                                        }
                                    }
                                ],
                                
                                [
                                    'attribute' => 'created_date',
                                    'label' => 'Created date',
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
                                    'attribute' => 'updated_date',
                                    'label' => 'Updated date',
                                    'headerOptions' => ['class' => 'icon-sort'],
                                    'content' => function ($data) {
                                        if (!empty($data['updated']) && $data['updated'] != '0000-00-00 00:00:00') {
                                            return $data['updated'];
                                        } else {
                                            return '';
                                        }
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