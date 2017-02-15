<?php

use yii\helpers\Url;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;
use common\models\Category;

Yii::$app->view->title = 'List Question';
?>
<div class="page-title">
    <div class="title_left">
        <h3>List Question</h3>
    </div>
</div>
<div class="clearfix"></div>
<div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel">
            <div class="x_content">
                <?php $form = ActiveForm::begin([
                    'action' => ['question/index'],
                    'method' => 'get'
                ]); ?>
                <form class="form-horizontal form-label-left">
                    <div class="col-md-4 col-sm-12 col-xs-12 form-group">
                        <?= Html::activeDropDownList($formSearch, 'category_id_1', $rootCat, ['prompt' => 'Select category', 'class' => 'form-control col-md-7 col-xs-12 select-root-cat']); ?>
                    </div>
                    
                    <div class="col-md-4 col-sm-12 col-xs-12 form-group">
                        <?= Html::activeDropDownList($formSearch, 'category_id_2', [], ['prompt' => 'Select sub1 category', 'class' => 'form-control col-md-7 col-xs-12 select-sub1-cat']); ?>
                    </div>
                    
                    <div class="col-md-4 col-sm-12 col-xs-12 form-group">
                        <?= Html::activeDropDownList($formSearch, 'category_id_3', [], ['prompt' => 'Select sub2 category', 'class' => 'form-control col-md-7 col-xs-12 select-sub2-cat']); ?>
                    </div>
                    
                    <div class="col-md-4 col-sm-12 col-xs-12 form-group">
                        <?= Html::activeDropDownList($formSearch, 'category_id_4', [], ['prompt' => 'Select sub3 category', 'class' => 'form-control col-md-7 col-xs-12']); ?>
                    </div>
                    
                    <div class="col-md-4 col-sm-12 col-xs-12 form-group">
                        <?= Html::activeTextInput($formSearch, 'question', ['class' => 'form-control col-md-7 col-xs-12', 'placeholder' => 'question']); ?>
                    </div>
                    
                    <div class="col-md-4 col-sm-12 col-xs-12 form-group">
                        <?= Html::submitButton('Search', ['class' => 'btn btn-warning']) ?>
                        <a class="btn btn-default" href="<?php echo Url::to(['/question/index']);?>">Clear</a>
                    </div>
                </form>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>

    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel">
            <div class="x_content">
                <div id="datatable_wrapper" class="table-responsive">
                    <?php if ($dataProvider->getTotalCount() == 0) :?>
                    <p class="txtWarning"><span class="iconNo">Data does not exist</span></p>
                    <?php else : ?>
                    <?php Pjax::begin();?>
                    <?= GridView::widget([
                        'dataProvider' => $dataProvider,
                        'layout' => '<div class="mBoxitem_listinfo">{summary}</div>{items}<div class="mBoxitem_listinfo">'
                                    . '<div id="paging" class="light-theme simple-pagination">{pager}</div></div>',
                        'summary' => '<div class="pageList_data"><strong>ALL {totalCount} Item {begin} ～ {end}</strong>'
                                    .'</div><div class="pageList_del"><div class="pageList_del_item"></div></div>',
                        'columns' => [
                            [
                                'attribute'=>'quiz_id',
                                'label'=>'Question ID',
                                'headerOptions' => ['class' => 'icon-sort'],
                                'content' => function ($data) {
                                    return '<a data-pjax="0" href="'.Url::to(['/question/detail',
                                        'quizId' => $data["quiz_id"]]).'">'.$data['quiz_id'].'</a>';
                                }
                            ],
                            [
                                'attribute'=>'question',
                                'label'=>'Question',
                                'headerOptions' => ['class' => 'icon-sort'],
                                'content' => function ($data) {
                                    return $data['question'];
                                }
                            ],
                            [
                                'attribute'=>'category_id_1',
                                'label'=>'Category',
                                'headerOptions' => ['class' => 'icon-sort'],
                                'content' => function ($data) {
                                    return Category::getDetailNameCategory($data['category_id_1']);
                                }
                            ],
                            [
                                'attribute' => 'category_id_2',
                                'label' => 'Sub1 Category',
                                'headerOptions' => ['class' => 'icon-sort'],
                                'content' => function ($data) {
                                    return Category::getDetailNameCategory($data['category_id_2']);
                                }
                            ],
                            [
                                'attribute' => 'category_id_3',
                                'label' => 'Sub2 Category',
                                'headerOptions' => ['class' => 'icon-sort'],
                                'content' => function ($data) {
                                    return Category::getDetailNameCategory($data['category_id_3']);
                                }
                            ],
                            [
                                'attribute' => 'category_id_4',
                                'label' => 'Sub3 Category',
                                'headerOptions' => ['class' => 'icon-sort'],
                                'content' => function ($data) {
                                    return Category::getDetailNameCategory($data['category_id_4']);
                                }
                            ],
                            
                            [
                                'attribute' => '',
                                'label' => '#',
                                'headerOptions' => ['class' => 'icon-sort'],
                                'content' => function ($data) {
                                    return '<div class="action"><a data-pjax="0" href="'.Url::to(['/question/save',
                                                'userId' => $data["quiz_id"]]).'"><i class="fa fa-edit"></i></a>'. '&nbsp&nbsp&nbsp'
                                            . '<a data-pjax="0" href="'.Url::to(['/question/delete',
                                                'userId' => $data["quiz_id"]]).'"><i class="fa fa-trash-o"></i></a></div>';
                                }
                            ],
                        ],
                        'tableOptions' =>['class' => 'table table-striped table-bordered'],
                        'pager' => [
                            'prevPageLabel'=>'Prev',
                            'nextPageLabel'=>'Next',
                            'activePageCssClass' => 'paginate_button active',
                            'disabledPageCssClass' => 'paginate_button previous disabled',
                            'options' => [
                                'class' => 'pagination',
                                'id' => 'pager-container',
                            ],
                        ],
                    ]); ?>
                    <?php Pjax::end(); ?>
                    <?php endif;?>
                </div>
            </div>
        </div>
    </div>
</div>