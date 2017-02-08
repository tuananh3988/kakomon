<?php

/* @var $this yii\web\View */

$this->title = 'Wellcome Purelamo!';

use kartik\tree\TreeView;
use kartik\tree\models\Tree;
echo TreeView::widget([
    // single query fetch to render the tree
    'query'             => Tree::find()->addOrderBy('root, lft'), 
    'headingOptions'    => ['label' => 'Categories'],
    'isAdmin'           => false,                       // optional (toggle to enable admin mode)
    'displayValue'      => 1,                           // initial display value
    //'softDelete'      => true,                        // normally not needed to change
    //'cacheSettings'   => ['enableCache' => true]      // normally not needed to change
]);
?>
<div class="site-index">
    <div class="jumbotron">
        <h1>Wellcome Purelamo!</h1>
    </div>
</div>
