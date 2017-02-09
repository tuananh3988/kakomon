<?php

/* @var $this yii\web\View */

$this->title = 'Category!';


?>
<div class="row">
    <?= $this->render('_item/list', ['listMenu' => $listMenu]); ?>
    <?= $this->render('_item/detail', []); ?>
</div>
