<?php

/* @var $this \yii\web\View */
/* @var $content string */

use backend\assets\AppAsset;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use common\widgets\Alert;
use yii\helpers\Url;
AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <?= Html::csrfMetaTags() ?>
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= Html::encode($this->title) ?></title>
    <script src="<?= Url::base(); ?>/vendors/jquery/dist/jquery.min.js"></script>
    <?php $this->head() ?>
</head>
<body class="nav-md">
<?php $this->beginBody() ?>
<div class="container body" id="AllWidth">
    <div class="main_container">
        <div class="col-md-3 left_col">
          <div class="left_col scroll-view">
            <div class="navbar nav_title" style="border: 0;">
              <a href="<?= Url::to(['']); ?>" class="site_title"><i class="fa fa-paw"></i> <span>Kakomon</span></a>
            </div>
            <div class="clearfix"></div>
            <br />
            <!-- sidebar menu -->
            <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
              <div class="menu_section">
                <h3>General</h3>
                <ul class="nav side-menu">
                    <li><a href="<?= Url::to(['/']); ?>"><i class="fa fa-home"></i>&nbsp;&nbsp;Home </a>
                    </li>
                    <li><a href="<?= Url::to(['/category/index']); ?>"><i class="glyphicon glyphicon-tags"></i> &nbsp;&nbsp;&nbsp;&nbsp;Category </a>
                    </li>
                    
                    <li class="<?= (Yii::$app->controller->id == 'question') ? 'active' : '';?>">
                        <a href="javascript:void(0)"><i class="glyphicon glyphicon-question-sign"></i> &nbsp;&nbsp;&nbsp;&nbsp;Question  <span class="fa fa-chevron-down"></span></a>
                        <ul class="nav child_menu" style="<?= (Yii::$app->controller->id == 'question') ? 'display: block;' : '';?>">
                            <li><a href="<?= Url::to(['/question/index']); ?>"><i class="glyphicon glyphicon-th-list"></i>&nbsp;&nbsp;&nbsp;List Question</a></li>
                            <li><a href="<?= Url::to(['/question/save']); ?>"><i class="glyphicon glyphicon-plus"></i>&nbsp;&nbsp;&nbsp;Add Question</a></li>
                        </ul>
                    </li>
                    
                    
                    <li class="<?= (Yii::$app->controller->id == 'quick') ? 'active' : '';?>">
                        <a href="javascript:void(0)"><i class="glyphicon glyphicon-question-sign"></i> &nbsp;&nbsp;&nbsp;&nbsp;Quick Question  <span class="fa fa-chevron-down"></span></a>
                        <ul class="nav child_menu" style="<?= (Yii::$app->controller->id == 'quick') ? 'display: block;' : '';?>">
                            <li><a href="<?= Url::to(['/quick/index']); ?>"><i class="glyphicon glyphicon-th-list"></i>&nbsp;&nbsp;&nbsp;List Quick Question</a></li>
                            <li><a href="<?= Url::to(['/quick/save']); ?>"><i class="glyphicon glyphicon-plus"></i>&nbsp;&nbsp;&nbsp;Add Quick Question</a></li>
                        </ul>
                    </li>
                    
                    <li><a href="<?= Url::to(['/collect/index']); ?>"><i class="glyphicon glyphicon-question-sign"></i> &nbsp;&nbsp;&nbsp;&nbsp;Collect Question</a>
                        
                    <li class="<?= (Yii::$app->controller->id == 'exam') ? 'active' : '';?>">
                        <a href="javascript:void(0)"><i class="glyphicon glyphicon-education"></i> &nbsp;&nbsp;&nbsp;&nbsp;Exam  <span class="fa fa-chevron-down"></span></a>
                        <ul class="nav child_menu" style="<?= (Yii::$app->controller->id == 'exam') ? 'display: block;' : '';?>">
                            <li><a href="<?= Url::to(['/exam/index']); ?>"><i class="glyphicon glyphicon-th-list"></i>&nbsp;&nbsp;&nbsp;List Exam</a></li>
                            <li><a href="<?= Url::to(['/exam/save']); ?>"><i class="glyphicon glyphicon-plus"></i>&nbsp;&nbsp;&nbsp;Add Exam</a></li>
                        </ul>
                    </li>
                    
                    <li><a href="<?= Url::to(['/csv/index']); ?>"><i class="fa fa-cloud-upload"></i> &nbsp;CSV </a>
                    <li><a href="<?= Url::to(['/notification/index']) ?>"><i class="fa fa-connectdevelop"></i> &nbsp;Push Notification</a></li>
                </ul>
              </div>
            </div>
            <!-- /sidebar menu -->
          </div>
        </div>

        <!-- top navigation -->
        <div class="top_nav">
          <div class="nav_menu">
            <nav class="" role="navigation">
              <div class="nav toggle">
                <a id="menu_toggle"><i class="fa fa-bars"></i></a>
              </div>

              <ul class="nav navbar-nav navbar-right">
                  <?php if(!Yii::$app->user->isGuest) : ?>
                        <li class="">
                          <a href="javascript:;" class="user-profile dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                <?php echo Yii::$app->user->identity->name;?>
                            <span class=" fa fa-angle-down"></span>
                          </a>
                          <ul class="dropdown-menu dropdown-usermenu pull-right">
                            <li><a href="<?= Url::to(['/site/logout']) ?>" class=""><i class="fa fa-sign-out pull-right"></i>Log Out</a></li>
                          </ul>
                        </li>
                    <?php else : ?>
                        <?= Html::a('Login', ['/site/login'], ['class'=>'btn btn-primary']) ?>
                    <?php endif;?>
              </ul>
            </nav>
          </div>
        </div>
        <!-- /top navigation -->
        <div class="right_col" role="main">
            <?= $content ?>
        </div>
    </div>
</div>
<?php $this->endBody() ?>
<script type="text/javascript" src="<?= Yii::$app->request->baseUrl; ?>/js/ajax-modal-popup.js"></script>
</body>
</html>
<?php $this->endPage() ?>
