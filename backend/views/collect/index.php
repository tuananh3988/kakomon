<?php

use yii\helpers\Url;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;
use common\models\Category;
use yii\web\Session;

Yii::$app->view->title = 'List Question Collect';

$subCat1 = [];
$subCat2 = [];
$subCat3 = [];
if ($formSearch->category_main_id) {
    $subCat1 = Category::getsubcategory($formSearch->category_main_id);
}
if ($formSearch->category_a_id) {
    $subCat2 = Category::getsubcategory($formSearch->category_a_id);
}
if ($formSearch->category_b_id) {
    $subCat3 = Category::getsubcategory($formSearch->category_b_id);
}

?>
<div class="page-title">
    <div class="title_left">
        <h3>List Question</h3>
    </div>
</div>
<div class="clearfix"></div>
<!-- complete message -->
<?php if (Yii::$app->session->hasFlash('message_delete')) : ?>
    <div class="alert alert-danger alert-dismissible fade in" role="alert"><?= Yii::$app->session->getFlash('message_delete') ?></div>
<?php endif; ?>
<!-- /complete message -->

<div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel">
            <div class="x_content">
                <?php
                $form = ActiveForm::begin([
                            'action' => ['collect/index'],
                            'method' => 'get'
                ]);
                ?>
                <form class="form-horizontal form-label-left">
                    <div class="col-md-4 col-sm-12 col-xs-12 form-group">
                    <?= Html::activeDropDownList($formSearch, 'category_main_id', $rootCat, ['prompt' => 'Select category', 'class' => 'form-control col-md-7 col-xs-12 select-root-cat']); ?>
                    </div>

                    <div class="col-md-4 col-sm-12 col-xs-12 form-group">
                    <?= Html::activeDropDownList($formSearch, 'category_a_id', $subCat1, ['prompt' => 'Select sub1 category', 'class' => 'form-control col-md-7 col-xs-12 select-sub1-cat']); ?>
                    </div>

                    <div class="col-md-4 col-sm-12 col-xs-12 form-group">
                    <?= Html::activeDropDownList($formSearch, 'category_b_id', $subCat2, ['prompt' => 'Select sub2 category', 'class' => 'form-control col-md-7 col-xs-12 select-sub2-cat']); ?>
                    </div>

                    <div class="col-md-4 col-sm-12 col-xs-12 form-group">
                    <?= Html::activeTextInput($formSearch, 'question', ['class' => 'form-control col-md-7 col-xs-12', 'placeholder' => 'question']); ?>
                    </div>

                    <div class="col-md-4 col-sm-12 col-xs-12 form-group">
                        <?= Html::submitButton('Search', ['class' => 'btn btn-warning']) ?>
                        <a class="btn btn-default" href="<?php echo Url::to(['/collect/index']); ?>">Clear</a>
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
                <?= Html::hiddenInput('idQuestion', '', ['id' => 'id-delete']) ?>
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
                                        'id' => $model['quiz_id'], 
                                        'onclick' => 'location.href="'
                                            . Yii::$app->urlManager->createUrl('collect/detail') 
                                            . '/"+(this.id);'
                                    ];
                            },
                            'columns' => [
                                [
                                    'attribute' => 'quiz_id',
                                    'label' => 'Question ID',
                                    'headerOptions' => ['class' => 'icon-sort'],
                                    'content' => function ($data) {
                                        return '<a data-pjax="0" href="' . Url::to(['/collect/detail',
                                                    'quizId' => $data["quiz_id"]]) . '">' . $data['quiz_id'] . '</a>';
                                    }
                                ],
                                [
                                    'attribute' => 'question',
                                    'label' => 'Question',
                                    'headerOptions' => ['class' => 'icon-sort'],
                                    'content' => function ($data) {
                                        return $data['question'];
                                    }
                                ],
                                [
                                    'attribute' => 'category_main_id',
                                    'label' => 'Category',
                                    'headerOptions' => ['class' => 'icon-sort'],
                                    'content' => function ($data) {
                                        return Category::getDetailNameCategory($data['category_main_id']);
                                    }
                                ],
                                [
                                    'attribute' => 'category_a_id',
                                    'label' => 'Sub1 Category',
                                    'headerOptions' => ['class' => 'icon-sort'],
                                    'content' => function ($data) {
                                        return Category::getDetailNameCategory($data['category_a_id']);
                                    }
                                ],
                                [
                                    'attribute' => 'category_b_id',
                                    'label' => 'Sub2 Category',
                                    'headerOptions' => ['class' => 'icon-sort'],
                                    'content' => function ($data) {
                                        return Category::getDetailNameCategory($data['category_b_id']);
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
                                return '<div class="action"><a data-pjax="0" href="javascript:void(0)" onclick="ConfirmDeleteQuestion(event, '.$data["quiz_id"].')"><i class="fa fa-trash-o"></i></a></div>';
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