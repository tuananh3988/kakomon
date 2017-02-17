<?php

namespace backend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use backend\models\LoginForm;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\web\Session;
use common\models\Exam;

class ExamController extends Controller {

    public function behaviors() {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['index', 'detail', 'save'],
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

    public function actionIndex() {
        return $this->render('index', [
        ]);
    }

    /*
     * Auth : 
     * 
     * Create Date : 10-02-2017
     */
    
    public function actionDetail($examId){
        return $this->render('detail', []);
    }
    
    public function actionSave($examId = NULL) {
        $session = Yii::$app->session;
        $request = Yii::$app->request;
        $exam = new Exam();
        $flag = 0;
        if (!empty($examId)) {
            $flag = 1;
        }
        if ($request->isPost) {
            $dataPost = $request->Post();
            $exam->load($dataPost);
            
        }
        return $this->render('save', [
            'exam' => $exam,
            'flag' => $flag
        ]);
    }
}
