<?php

namespace backend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\web\Session;
use common\models\Notification;
use common\models\Collect;

class NotificationController extends Controller {
    public function behaviors() {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['index', 'add-push-collect'],
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
    
    /*
     * Add push Collect
     * 
     * Auth : 
     * Created : 30/05/2017
     */
    
    public function actionAddPushCollect() {
        $request = Yii::$app->request;
        $collect = new Collect();
        if ($request->isPost) {
            $dataPost = $request->Post();
            $collect->load($dataPost);
            if ($collect->validate()) {
                $collect->save();
                $modelNotification = new Notification();
                $modelNotification->type = Notification::TYPE_COLLECT_QUIZ;
                $modelNotification->related_id = $collect->collect_id;
                $modelNotification->save();
                $message = 'Your create successfully collect question!';
                
                Yii::$app->session->setFlash('sucess_notification',$message);
                return Yii::$app->response->redirect(['/notification/index']);
            }
        }
        return $this->render('save', [
            'collect' => $collect
        ]);
    }
}