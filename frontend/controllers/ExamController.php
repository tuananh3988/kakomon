<?php
namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\QueryParamAuth;
use common\models\Exam;
/**
 * Site controller
 */
class ExamController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'info' => ['get'],
                    'detail' => ['get']
                ],
            ],
            'authenticator' => [
                'class' => CompositeAuth::className(),
                'except' => [],
                'authMethods' => [
                    QueryParamAuth::className(),
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }
    
    /*
     * List category
     * 
     * Auth : 
     * Create : 20-04-2017
     */
    
    public function actionInfo()
    {
        $request = Yii::$app->request;
        $param = $request->queryParams;
        
        $modelExam = new Exam();
        $modelExam->setAttributes($param);
        $modelExam->scenario  = Exam::SCENARIO_EXAM_DETAIL;
        if (!$modelExam->validate()) {
            return [
                    'status' => 400,
                    'messages' => $modelExam->errors
                ];
        }
        
        $examDetail = Exam::findOne(['exam_id' => $modelExam->exam_id]);
        return [
            'status' => 200,
            'data' => [
                'exam_id' => (int) $examDetail->exam_id,
                'name' => $examDetail->name,
                'status' => (int) $examDetail->status,
                'total_quiz' => (int) $examDetail->total_quiz,
                'start_date' => $examDetail->start_date,
                'end_date' => $examDetail->end_date,
                'created_date' => $examDetail->created_date
            ]
            
        ];
    }
    
    /*
     * Detail
     * 
     * Auth : 
     * Created : 20-04-2017
     */
    
    public function actionDetail()
    {
        $request = Yii::$app->request;
        $param = $request->queryParams;
        
        $modelExam = new Exam();
        $modelExam->setAttributes($param);
        $modelExam->scenario  = Exam::SCENARIO_EXAM_DETAIL;
        if (!$modelExam->validate()) {
            return [
                    'status' => 400,
                    'messages' => $modelExam->errors
                ];
        }
        $listQuiz = $modelExam->getListQuizIdByExam();
        //return no data
        if (count($listQuiz) == 0) {
            return [
                'status' => 204,
                'message' => \Yii::t('app', 'data not found')
            ];
        }
        $data = [];
        foreach ($listQuiz as $key => $value) {
            $data[] = $value['quiz_id'];
        }
        return [
            'status' => 200,
            'exam_id' => (int)$modelExam->exam_id,
            'data' => $data
        ];
    }
}