<?php
use yii\helpers\Html;
use yii\helpers\Url;
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= Html::encode($this->title) ?></title>
    <link href="<?= Yii::$app->request->baseUrl; ?>/vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="<?= Yii::$app->request->baseUrl; ?>/vendors/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <link href="<?= Yii::$app->request->baseUrl; ?>/build/css/custom.min.css" rel="stylesheet" type="text/css">
</head>
<body class="nav-md">
<?php $this->beginBody() ?>
<div class="container body">
    <?= $content ?>
</div>
<?php $this->endBody() ?>
    <script src="<?= Url::base(); ?>/vendors/jquery/dist/jquery.min.js"></script>
    <script src="<?= Url::base(); ?>/vendors/bootstrap/dist/js/bootstrap.min.js"></script>
    <script src="<?= Url::base(); ?>/vendors/fastclick/lib/fastclick.js"></script>
    <script src="<?= Url::base(); ?>/vendors/nprogress/nprogress.js"></script>
    <script src="<?= Url::base(); ?>/build/js/custom.min.js"></script>
</body>
</html>
<?php $this->endPage() ?>
