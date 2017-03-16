<?php
namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\QueryParamAuth;
use frontend\models\Question;
use common\models\Category;
use common\models\Quiz;
use common\models\MemberQuizHistory;
use common\components\Utility;
use common\models\MemberQuizSearchHistory;

/**
 * Site controller
 */
class QuizController extends Controller
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
                    'list' => ['search', 'total', 'year', 'history-search']
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
    
    /*
     * function search
     * 
     * Auth : 
     * Create : 16-03-2016
     */
    
    public function actionSearch()
    {
        $request = Yii::$app->request;
        $param = $request->queryParams;
        $limit = isset($param['limit']) ? $param['limit'] : Yii::$app->params['limit']['quiz'];
        $offset = isset($param['offset']) ? $param['offset'] : Yii::$app->params['offset']['quiz'];
        
        $modelQuiz = new Question();
        $modelQuiz->setAttributes($param);
        if (!$modelQuiz->validate()) {
            return [
                    'status' => 400,
                    'messages' => $modelQuiz->errors
                ];
        }
        //get firt category
        $firtCat = Category::find()->where(['parent_id' => 0])->orderBy(['cateory_id' => SORT_ASC])->one();
        if (!$firtCat) {
            return [
                'status' => 204,
                'message' => \Yii::t('app', 'data not found')
            ];
        }
        $modelQuiz->category_main_id =  !empty($param['category_main_id']) ? $param['category_main_id'] : $firtCat['cateory_id'];
        
        //insert table member_quiz_search_history
        $modelQuiz->insertHistorySearch();
        
        $listQuiz = $modelQuiz->getListQuiz($limit, $offset);
        //return if not found data
        if (count($listQuiz) == 0) {
            return [
                'status' => 204,
                'message' => \Yii::t('app', 'data not found')
            ];
        }
        
        //return data
        $data = [];
        foreach ($listQuiz as $key => $value) {
            $data[] = [
                'category_main_id' => $modelQuiz->category_main_id,
                'category_main_name' => Category::getDetailNameCategory($modelQuiz->category_main_id),
                'sub_cat' => Quiz::renderListSubCat($value['category_a_id'], $value['category_b_id']),
                'quiz_id' => (int)$value['quiz_id'],
                'question' => $value['question'],
                'list_ans' => $this->renderListAnsHistory($value['quiz_id'])
            ];
        }
        
        //return success
        $total = $modelQuiz->getListQuiz($limit, $offset, true);
        $offsetReturn = Utility::renderOffset($total, $limit, $offset);
        return [
            'status' => 200,
            'count' => (int)$total,
            'offset' => $offsetReturn,
            'data' => $data
        ];
    }
    
    /*
     * render list ans
     * 
     * Auth : 
     * Create : 16-03-2017
     */
    public function renderListAnsHistory($quizId){
        $listAns = [];
        $list = MemberQuizHistory::getTwoRecordAnsWithMember($quizId);
        if (count($list) > 0) {
            foreach ($list as $key => $value) {
                $listAns[] = [
                    'member_quiz_history_id' => (int)$value['member_quiz_history_id'],
                    'correct_flag' => (int)$value['correct_flag']
                ];
            }
        }
        
        return $listAns;
    }
    
    
    /*
     * function lít total
     * 
     * Auth : 
     * Create : 16-03-2016
     */
    
    public function actionTotal()
    {
        $request = Yii::$app->request;
        $param = $request->queryParams;
        $modelQuiz = new Question();
        $modelQuiz->setAttributes($param);
        if (!$modelQuiz->validate()) {
            return [
                    'status' => 400,
                    'messages' => $modelQuiz->errors
                ];
        }
        //get firt category
        $firtCat = Category::find()->where(['parent_id' => 0])->orderBy(['cateory_id' => SORT_ASC])->one();
        if (!$firtCat) {
            return [
                'status' => 204,
                'message' => \Yii::t('app', 'data not found')
            ];
        }
        $modelQuiz->category_main_id =  !empty($param['category_main_id']) ? $param['category_main_id'] : $firtCat['cateory_id'];
        $modelQuiz->type_quiz = Question::TYPE_ALL;
        $totalAll = $modelQuiz->getListQuiz(null, null, true);
        $modelQuiz->type_quiz = Question::TYPE_RIGHT;
        $totalRight = $modelQuiz->getListQuiz(null, null, true);
        $modelQuiz->type_quiz = Question::TYPE_WRONG;
        $totalWrong = $modelQuiz->getListQuiz(null, null, true);
        $modelQuiz->type_quiz = Question::TYPE_DO_NOT;
        $totalDoNot = $modelQuiz->getListQuiz(null, null, true);
        
        return [
            'status' => 200,
            'data' => [
                'totalAll' => (int)$totalAll,
                'totalRight' => (int)$totalRight,
                'totalWrong' => (int)$totalWrong,
                'totalDoNot' => (int)$totalDoNot
            ]
        ];
    }
    
    /*
     * function lít total
     * 
     * Auth : 
     * Create : 16-03-2016
     */
    
    public function actionYear()
    {
        $modelQuiz = new Question();
        $listYear = $modelQuiz->getListYear();
        if (count($listYear) == 0) {
            return [
                'status' => 204,
                'message' => \Yii::t('app', 'data not found')
            ];
        }
        // return all data
        return [
            'status' => 200,
            'data' => $listYear
        ];
    }
    
    /*
     * List history search
     * 
     * Auth : 
     * Create : 16-03-2016
     */
    
    public function actionHistorySearch()
    {
        $request = Yii::$app->request;
        $param = $request->queryParams;
        $limit = isset($param['limit']) ? $param['limit'] : Yii::$app->params['limit']['quiz'];
        $offset = isset($param['offset']) ? $param['offset'] : Yii::$app->params['offset']['quiz'];
        
        $modelHistorySearch = new MemberQuizSearchHistory();
        $listHistorySearch = $modelHistorySearch->getListHistorySearch($limit, $offset);
        //return if not found data
        if (count($listHistorySearch) == 0) {
            return [
                'status' => 204,
                'message' => \Yii::t('app', 'data not found')
            ];
        }
        
        //return data
        $data = [];
        foreach ($listHistorySearch as $key => $value) {
            $data[] = [
                'member_quiz_search_history_id' => $value['member_quiz_search_history_id'],
                'quiz_class' => $value['quiz_class'],
                'category_main_id' => [
                    'id' => $value['category_main_id'],
                    'name' => Category::getDetailNameCategory($value['category_main_id'])
                ],
                'category_a_id' => [
                    'id' => $value['category_a_id'],
                    'name' => Category::getDetailNameCategory($value['category_a_id'])
                ],
                'category_b_id' => [
                    'id' => $value['category_b_id'],
                    'name' => Category::getDetailNameCategory($value['category_b_id'])
                ],
                'quiz_year' => $value['quiz_year'],
                'type_quiz' => $value['type'],
            ];
        }
        
        //return success
        $total = $modelHistorySearch->getListHistorySearch($limit, $offset, true);
        $offsetReturn = Utility::renderOffset($total, $limit, $offset);
        return [
            'status' => 200,
            'count' => (int)$total,
            'offset' => $offsetReturn,
            'data' => $data
        ];
    }
}