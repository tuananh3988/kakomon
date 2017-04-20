<?php

namespace backend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\web\Session;
use common\models\Notification;

class NotificationController extends Controller {
    public function behaviors() {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['index'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                //'logout' => ['post'],
                ],
            ],
        ];
    }
    
    /*
     *
     * Auth : 
     * Created : 20-04-2017 
     */
    public function actionIndex() {
        $request = Yii::$app->request;
        $formSearch = new Notification();
        
        $dataProvider = $formSearch->getData();
        return $this->render('index', [
            'dataProvider' => $dataProvider
        ]);
    }
}