<?php

$this->title = 'Category!';

?>
<div class="row">
    <?= $this->render('_item/list', ['listMenu' => $listMenu]); ?>
    <?= $this->render('_item/detail', ['firstMenu' => $firstMenu]); ?>
</div>
