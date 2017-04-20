<?php

use yii\helpers\Url;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;
use yii\web\Session;
use common\models\Exam;
use common\models\Notification;
Yii::$app->view->title = 'List Notification';

?>

<div class="page-title">
    <div class="title_left">
        <h3>List Notification</h3>
    </div>
</div>
<div class="clearfix"></div>

<div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel">
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
                                    'attribute' => 'notification_id',
                                    'label' => 'Notification ID',
                                    'headerOptions' => ['class' => 'icon-sort'],
                                    'content' => function ($data) {
                                        return $data['notification_id'];
                                    }
                                ],
                                [
                                    'attribute' => 'type',
                                    'label' => 'Type',
                                    'headerOptions' => ['class' => 'icon-sort'],
                                    'content' => function ($data) {
                                        if (array_key_exists($data['type'], Notification::$NOTIFICATION_TYPE)) {
                                            return Notification::$NOTIFICATION_TYPE[$data['type']];
                                        } else {
                                            return $data['type'];
                                        }
                                    }
                                ],
                                [
                                    'attribute' => 'title',
                                    'label' => 'Title',
                                    'headerOptions' => ['class' => 'icon-sort'],
                                    'content' => function ($data) {
                                        return Notification::getInfoNotification($data['type'], $data['related_id']);
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