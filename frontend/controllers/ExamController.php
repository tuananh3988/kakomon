<?php
namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\QueryParamAuth;
use common\models\Exam;
use common\models\MemberQuizHistory;
use common\models\ExamHistory;
use common\models\ExamQuiz;
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
                    'detail' => ['get'],
                    'finish' => ['post'],
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
        $listQuiz = $modelExam->getListQuizIdByExam();
        $dataQuiz = [];
        foreach ($listQuiz as $key => $value) {
            $dataQuiz[] = $value['quiz_id'];
        }
        return [
            'status' => 200,
            'data' => [
                'exam_id' => (int) $examDetail->exam_id,
                'name' => $examDetail->name,
                'status' => (int) $examDetail->status,
                'total_quiz' => (int) $examDetail->total_quiz,
                'start_date' => $examDetail->start_date,
                'end_date' => $examDetail->end_date,
                'created_date' => $examDetail->created_date,
                'data_quiz' => $dataQuiz
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
        $info = $modelExam->getInfoByExamId();
        $data = [];
        foreach ($listQuiz as $key => $value) {
            $data[] = $value['quiz_id'];
        }
        return [
            'status' => 200,
            'exam_id' => (int)$modelExam->exam_id,
            'contest_times' => (!empty($info)) ? (int)($info['contest_time'] + 1) : 1, 
            'flag_exam' => (!empty($info) && !empty($info['exam_history_id'])) ? 1 : 0,
            'data' => $data
        ];
    }
    
    /*
     * Finish exam
     * 
     * Auth : 
     * Created : 20-06-2017
     */
    public function actionFinish() {
        $request = Yii::$app->request;
        $dataPost = $request->post();
        $modelExam = new Exam();
        $modelExamQuiz = new ExamQuiz();
        $modelExam->setAttributes($dataPost);
        $modelExam->scenario  = Exam::SCENARIO_EXAM_FINISH;
        if (!$modelExam->validate()) {
            return [
                    'status' => 400,
                    'messages' => $modelExam->errors
                ];
        }
        // get total quiz correct ans
        $contestTimes = (int)$modelExam->contest_times;
        $modelMemberQuizHistory = new MemberQuizHistory();
        $totalAnsCorrect = $modelMemberQuizHistory->getTotalAnsCorrectByExam($modelExam->exam_id, $contestTimes);
        $totalNotAns = $modelMemberQuizHistory->getTotalNotAnsByExam($modelExam->exam_id, $contestTimes);
        $modelExamHistory = new ExamHistory();
        //save data
        $modelExamHistory->exam_id = $modelExam->exam_id;
        $modelExamHistory->member_id = Yii::$app->user->identity->member_id;
        $modelExamHistory->total_correct = $totalAnsCorrect;
        $modelExamHistory->total_not_doing = $totalNotAns;
        $modelExamHistory->contest_times = $contestTimes;
        
        if (!$modelExamHistory->save()) {
            throw new \yii\base\Exception( "System error" );
        }
        // get rank and rate
        $rankExam = $modelExamHistory->getRankExam($modelExamHistory->exam_history_id);
        
        $totalAnsCorrectInHistory = $modelExamHistory->getTotalAnsCorrectByExam($modelExam->exam_id);
        $totalQuiz = $modelExamQuiz->getCountQuizByIdExam($modelExam->exam_id);
        $totalUser = (int)$modelExamHistory->getTotalMemberJoinByExam($modelExam->exam_id);
        if ($totalUser == 0) {
            $totalUser = 1;
        }
        $rateCorrect = round($totalAnsCorrectInHistory / ($totalUser * $totalQuiz), 4);
        
        $modelExamHistory->rank_exam = $rankExam[0]['rank'];
        $modelExamHistory->rate_correct = $rateCorrect;
        
        if (!$modelExamHistory->save()) {
            throw new \yii\base\Exception( "System error" );
        }
        return  [
            'status' => 200
        ];
    }
}