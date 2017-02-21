<?php

namespace backend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\web\Session;
use common\models\Exam;
use common\models\ExamQuiz;

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
        $request = Yii::$app->request;
        $formSearch = new Exam();
        $param = $request->queryParams;
        if (!empty($param['Exam'])) {
            $formSearch->setAttributes($param['Exam']);
        }
        $dataProvider = $formSearch->getData();
        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'formSearch' => $formSearch
        ]);
    }

    /*
     * Auth : 
     * 
     * Create Date : 10-02-2017
     */
    
    public function actionDetail($examId){
        $session = Yii::$app->session;
        $request = Yii::$app->request;
        
        $model = new Exam();
        $examItem = $model->find()->where(['exam_id' => $examId])->one();
        $modelExamQuiz = new ExamQuiz();
        $dataProvider = $modelExamQuiz->listQuiz($examId);
        $totalQuiz = ExamQuiz::getCountQuizByIdExam($examId);
        if (empty($examItem)) {
            return Yii::$app->response->redirect(['/error/error']);
        }
        if ($request->isPost) {
            $examItem->status = 1;
            $examItem->start_date = date('Y-m-d H:i:s');
            $examItem->save(false);
            $message = 'You start exam!';
            Yii::$app->session->setFlash('sucess_exam',$message);
        }
        return $this->render('detail', [
            'examItem' => $examItem,
            'totalQuiz' => $totalQuiz,
            'dataProvider' => $dataProvider
        ]);
    }
    
    public function actionSave($examId = NULL) {
        $session = Yii::$app->session;
        $request = Yii::$app->request;
        $exam = new Exam();
        $flag = 0;
        if (!empty($examId)) {
            $exam = Exam::find()->where(['exam_id' => $examId])->one();
            $flag = 1;
        }
        if ($request->isPost) {
            $dataPost = $request->Post();
            $exam->load($dataPost);
            if ($exam->validate()) {
                $exam->save();
                if ($flag == 0) {
                    $message = 'Your create successfully exam!';
                } elseif($flag == 1){
                    $message = 'You update successfully exam!';
                }
                Yii::$app->session->setFlash('sucess_exam',$message);
                return Yii::$app->response->redirect(['/exam/index']);
            }
        }
        return $this->render('save', [
            'exam' => $exam,
            'flag' => $flag
        ]);
    }
}
