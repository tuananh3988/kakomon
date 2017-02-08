<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;

$this->title = 'Login';
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="animate form login_form">
    <section class="login_content">
        <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>
        <h1><?= Html::encode($this->title) ?></h1>
        <div class = "mBoxitem_txt txtWarning">
            <?= $form->errorSummary($model, ['header' => '']); ?>
        </div>
        <div>
            <?= $form->field($model, 'username', ['template' => '{input}'])->textInput(['autofocus' => true, 'class' => 'form-control', 'placeholder' => 'Username']); ?>
        </div>
        <div>
            <?= $form->field($model, 'password', ['template' => '{input}'])->passwordInput(['autofocus' => true, 'class' => 'form-control', 'placeholder' => 'Password']); ?>
        </div>
        <div>
            <?= $form->field($model, 'captcha', ['template' => '{input}'])->widget(Captcha::classname(), ['imageOptions' => ['id' => 'my-captcha-image'],
                'template' => '{input}<img id="refresh-captcha" src="'.Yii::$app->request->baseUrl.'/images/refresh-icon.png">{image}']) ?>
        </div>
        <div class="clearfix"></div>
        <div>
            <?= Html::submitButton('Log in', ['class' => 'btn btn-default submit']); ?>
            
        </div>
        <div class="clearfix"></div>
        <?php ActiveForm::end(); ?>
    </section>
</div>
<script>
    $("#refresh-captcha").click(function(event){
        event.preventDefault();
        $("img[id$='my-captcha-image']").click();
    })
</script>