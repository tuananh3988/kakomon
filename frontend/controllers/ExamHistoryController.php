<?php
namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\QueryParamAuth;
use common\models\ExamHistory;
use common\models\ExamQuiz;
use common\models\MemberQuizHistory;
/**
 * Site controller
 */
class ExamHistoryController extends Controller
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
                    'list' => ['get'],
                    'detail' => ['get'],
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
     * List history exam
     * 
     * Auth : 
     * Create : 20-04-2017
     */
    
    public function actionList()
    {
        $modelExamHistory = new ExamHistory();
        $listExam = $modelExamHistory->getListExamByMember();
        //return no data
        if (count($listExam) == 0) {
            return [
                'status' => 204,
                'message' => \Yii::t('app', 'data not found')
            ];
        }
        $data = [];
        foreach ($listExam as $key => $value) {
            $totalUser = $modelExamHistory->getTotalMemberJoinByExam($value['exam_id']);
            $data[] = [
                'exam_id' => (int)$value['exam_id'],
                'flag_not_ans' => ($value['total_not_doing'] == 0) ? 0 : 1,
                'total_user' => (int)$totalUser,
                'rate_correct' => $value['rate_correct'] * 100,
                'rank_exam' => (int)$value['rank_exam'],
                'contest_times' => (int)$value['contest_times'],
                'title' => $value['name'],
                'date' => $value['created_date'],
            ];
        }
        return [
            'status' => 200,
            'data' => $data
        ];
    }
    
     /*
     * List history exam
     * 
     * Auth : 
     * Create : 20-04-2017
     */
    
    public function actionDetail()
    {
        $request = Yii::$app->request;
        $param = $request->queryParams;
        $modelExamHistory = new ExamHistory();
        $modelMemberQuizHistory = new MemberQuizHistory();
        $modelExamHistory->setAttributes($param);
        $modelExamHistory->scenario  = ExamHistory::SCENARIO_EXAM_HISTORY_DETAIL;
        if (!$modelExamHistory->validate()) {
            return [
                    'status' => 400,
                    'messages' => $modelExamHistory->errors
                ];
        }
        //get info exam
        $infoExam = ExamHistory::find()->where(['exam_id' => $modelExamHistory->exam_id, 'member_id' => Yii::$app->user->identity->member_id])->one();
        $totalUser = $modelExamHistory->getTotalMemberJoinByExam($modelExamHistory->exam_id);
        //get list category exam
        $modelExamQuiz = new ExamQuiz();
        $listCategory = $modelExamQuiz->getAllCategoryByExam($modelExamHistory->exam_id);
        //
        if (count($listCategory) == 0) {
            return [
                'status' => 204,
                'message' => \Yii::t('app', 'data not found')
            ];
        }
        $data = [];
        foreach ($listCategory as $key => $value) {
            $totalQuizCategory = $modelExamQuiz->getCountQuizByIdExam($modelExamHistory->exam_id, $value['cateory_id']);
            $totalQuizAnsCorrect = $modelMemberQuizHistory->getTotalAnsCorrectByExam($modelExamHistory->exam_id, $modelExamHistory->contest_times, $value['cateory_id']);
            $rateAnsCategory = round($totalQuizAnsCorrect / $totalQuizCategory, 4);
            $totalMediumQuizAnsCorrect = $this->getAllAnsCorrectByCategory($modelExamHistory->exam_id, $value['cateory_id']);
            $rateMediumAnsCategory = round($totalMediumQuizAnsCorrect / $totalQuizCategory, 4);
            $totalNotDoing = $modelMemberQuizHistory->getTotalNotAnsByExamAndCategory($modelExamHistory->exam_id, $modelExamHistory->contest_times, $value['cateory_id']);
            $data[] = [
                'cateory_id' => (int)$value['cateory_id'],
                'name_category' => $value['name'],
                'total_quiz_category' => (int)$totalQuizCategory,
                'total_quiz_ans_correct' => (int)$totalQuizAnsCorrect,
                'rate_ans_correct_category' => (float)($rateAnsCategory * 100),
                'total_medium_quiz_ans_correct' => (int)$totalMediumQuizAnsCorrect,
                'rate_medium_ans_category' => (float)($rateMediumAnsCategory * 100),
                'flag_not_ans' => ($totalNotDoing == 0) ? 0 : 1,
            ];
        }
        return  [
            'status' => 200,
            'exam_id' => $modelExamHistory->exam_id,
            'rate_correct' => $infoExam->rate_correct * 100,
            'rank_exam' => (int)$infoExam->rank_exam,
            'contest_times' => (int)$infoExam->contest_times,
            'total_user' => (int)$totalUser,
            'data' => $data
        ];
    }
    
    /*
     * Get total ans correct by category and exam
     * 
     * Auth : 
     * Created : 22-06-2017
     */
    public static function getAllAnsCorrectByCategory($examId , $categoryId) {
        $totalCorrect = 0;
        $totalMediumCorrect = 0;
        $modelExamQuiz = new ExamQuiz();
        $modelExamHistory = new ExamHistory();
        $modelMemberQuizHistory = new MemberQuizHistory();
        $listUser = $modelExamHistory->getTotalMemberJoinByExam($examId, false);
        $totalQuizCategory = $modelExamQuiz->getCountQuizByIdExam($examId, $categoryId);
        if (count($listUser) > 0) {
            foreach ($listUser as $key => $value) {
                $totalQuizAnsCorrect = $modelMemberQuizHistory->getTotalAnsCorrectByExam($examId, $value['contest_times'], $categoryId, $value['member_id']);
                $totalCorrect = $totalCorrect + $totalQuizAnsCorrect;
            }
        }
        $totalMediumCorrect = $totalCorrect / (count($listUser));
        return $totalMediumCorrect;
    }
}