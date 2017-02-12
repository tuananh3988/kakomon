<?php
namespace backend\components;

use Yii;
use yii\web\Session;

class setCategory extends \yii\base\Behavior
{
    public function events() {
        return [
        \Yii\web\Application::EVENT_BEFORE_ACTION => 'setCategory'
        ];
    }
    
    public function setCategory()
    {
        $session = Yii::$app->session;
        if (Yii::$app->controller->id !== 'category') {
            if (isset($session['category_id'])) {
                $session->set('category_id', NULL);
            }
        }
    }
}