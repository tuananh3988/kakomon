<?php

/* @var $this yii\web\View */

$this->title = 'Category!';

use kartik\tree\TreeView;
use common\models\Category;
echo TreeView::widget([
    // single query fetch to render the tree
    // use the Product model you have in the previous step
    'query' => Category::find()->addOrderBy('root, lft'), 
    'headingOptions' => ['label' => 'Categories'],
    'fontAwesome' => false,     // optional
    'isAdmin' => false,         // optional (toggle to enable admin mode)
    'displayValue' => 1,        // initial display value
    'softDelete' => true,       // defaults to true
    'cacheSettings' => [        
        'enableCache' => true   // defaults to true
    ],
    'iconEditSettings'=> [
        'show' => 'none',
    ],
    'defaultChildNodeIcon' => '',
    'defaultParentNodeIcon' => '',
    'defaultParentNodeOpenIcon' => '',
    'rootOptions' => [
        'label' => 'Category'
    ],
    'emptyNodeMsg' => 'No data category',
    'toolbar' => [
        TreeView::BTN_MOVE_UP => false,
        TreeView::BTN_MOVE_DOWN =>false,
        TreeView::BTN_MOVE_LEFT => false,
        TreeView::BTN_MOVE_RIGHT => false
    ]
]);
?>
