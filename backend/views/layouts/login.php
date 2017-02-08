<?php

use backend\assets\AppAsset;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <script src="<?= Url::base(); ?>/vendors/jquery/dist/jquery.min.js"></script>
    <link href="<?= Yii::$app->request->baseUrl; ?>/css/custom.css" rel="stylesheet" type="text/css">
    <?php $this->head() ?>
</head>
<body class="login">
<?php $this->beginBody() ?>
<div class="login_wrapper">
    <?= $content ?>
</div>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
