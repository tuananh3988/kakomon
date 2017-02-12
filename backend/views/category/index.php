<?php

$this->title = 'Category!';

?>
<input type="hidden" id="hidden-cat" value="<?= $firstCategory->id?>">
<div class="row">
    <?= $this->render('_item/list', ['listCategory' => $listCategory]); ?>
    <?= $this->render('_item/detail', ['firstCategory' => $firstCategory, 'breadCrumbs' => $breadCrumbs]); ?>
</div>
