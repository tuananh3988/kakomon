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
use common\models\Answer;
use common\models\MemberQuizHistory;
use common\components\Utility;
use common\models\MemberQuizSearchHistory;
use common\models\MemberQuizActivity;
use frontend\models\Ans;

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
                    'search' => ['get'],
                    'search-text' => ['get'],
                    'total' => ['get'],
                    'year' => ['get'],
                    'history-search' => ['get'],
                    'history-ans' => ['get'],
                    'history-ans-wrong' => ['get'],
                    'ans' => ['post'],
                    'add-question' => ['post']
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
        $modelQuiz->scenario  = Question::SCENARIO_LIST_QUIZ;
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
        $cateoryMainSearch[] = $firtCat['cateory_id'];
        $param['category_main_search'] = !empty($param['category_main_search']) ? $param['category_main_search'] : $cateoryMainSearch;
        $paramSearch = $this->renderParamSearch($param);
        
        //insert table member_quiz_search_history
        $modelQuiz->insertHistorySearch();
        
        $listQuiz = $modelQuiz->getListQuiz($paramSearch, $limit, $offset);
        
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
                'category_main_id' => (int)$value['category_main_id'],
                'category_main_name' => Category::getDetailNameCategory($value['category_main_id']),
                'sub_cat' => Quiz::renderListSubCat($value['category_a_id'], $value['category_b_id']),
                'quiz_id' => (int)$value['quiz_id'],
                'question' => $value['question'],
                'img_question' => Utility::getImage('question', $value['quiz_id'], null, true),
                'list_ans' => $this->renderListAnsHistory($value['quiz_id'])
            ];
        }
        
        //return success
        $total = $modelQuiz->getListQuiz($paramSearch, $limit, $offset, true);
        $offsetReturn = Utility::renderOffset($total, $limit, $offset);
        return [
            'status' => 200,
            'count' => (int)$total,
            'offset' => $offsetReturn,
            'data' => $data
        ];
    }
    
    /*
     * function search text
     * 
     * Auth : 
     * Create : 06-07-2017
     */
    
    public function actionSearchText()
    {
        $request = Yii::$app->request;
        $param = $request->queryParams;
        $limit = isset($param['limit']) ? $param['limit'] : Yii::$app->params['limit']['quiz'];
        $offset = isset($param['offset']) ? $param['offset'] : Yii::$app->params['offset']['quiz'];
        $keyWord = !empty($param['key_word']) ? $param['key_word'] : '';
        $modelQuiz = new Question();
        
        $listQuiz = $modelQuiz->getListQuizByText($keyWord, $limit, $offset);
        
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
                'category_main_id' => (int)$value['category_main_id'],
                'category_main_name' => Category::getDetailNameCategory($value['category_main_id']),
                'sub_cat' => Quiz::renderListSubCat($value['category_a_id'], $value['category_b_id']),
                'quiz_id' => (int)$value['quiz_id'],
                'question' => $value['question'],
                'img_question' => Utility::getImage('question', $value['quiz_id'], null, true),
                'list_ans' => $this->renderListAnsHistory($value['quiz_id'])
            ];
        }
        
        //return success
        $total = $modelQuiz->getListQuizByText($keyWord, $limit, $offset, true);
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
        $modelQuiz->scenario  = Question::SCENARIO_LIST_QUIZ;
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
        $cateoryMainSearch[] = $firtCat['cateory_id'];
        $param['category_main_search'] = !empty($param['category_main_search']) ? $param['category_main_search'] : $cateoryMainSearch;
        $paramSearch = $this->renderParamSearch($param);
        
        $modelQuiz->category_main_search =  !empty($param['category_main_search']) ? $param['category_main_search'] : $cateoryMainSearch;
        $modelQuiz->type_quiz = Question::TYPE_ALL;
        $totalAll = $modelQuiz->getListQuiz($paramSearch, null, null, true);
        $modelQuiz->type_quiz = Question::TYPE_RIGHT;
        $totalRight = $modelQuiz->getListQuiz($paramSearch, null, null, true);
        $modelQuiz->type_quiz = Question::TYPE_WRONG;
        $totalWrong = $modelQuiz->getListQuiz($paramSearch, null, null, true);
        $modelQuiz->type_quiz = Question::TYPE_DO_NOT;
        $totalDoNot = $modelQuiz->getListQuiz($paramSearch, null, null, true);
        
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
                'member_quiz_search_history_id' => (int)$value['member_quiz_search_history_id'],
                'quiz_class' => $value['quiz_class'],
                'category_main' => self::renderHistorySearch($value['category_main_id']),
                'category_a' => self::renderHistorySearch($value['category_a_id']),
                'category_b' => self::renderHistorySearch($value['category_b_id']),
                'quiz_year' => json_decode($value['quiz_year']),
                'type_quiz' => (int)$value['type'],
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
    
    /*
     * Quiz detail
     * 
     * Auth : 
     * Create : 17-03-2016
     */
    
    public function actionDetail()
    {
        $request = Yii::$app->request;
        $param = $request->queryParams;
        
        $modelQuiz = new Question();
        $modelQuiz->scenario  = Question::SCENARIO_DETAIL_QUIZ;
        $modelQuiz->setAttributes($param);
        if (!$modelQuiz->validate()) {
            return [
                    'status' => 400,
                    'messages' => $modelQuiz->errors
                ];
        }
        
        $quizDetail = Quiz::find()->where(['quiz_id' => $modelQuiz->quiz_id, 'type' => $modelQuiz->type, 'delete_flag' => Quiz::QUIZ_ACTIVE])->one();
        if (!$quizDetail) {
            return [
                'status' => 204,
                'message' => \Yii::t('app', 'data not found')
            ];
        }
        $listAns = [];
        for ($i = 1; $i <= Answer::MAX_ANS; $i++) {
            $ansDetail = Answer::findOne(['quiz_id' => $quizDetail->quiz_id, 'order' => $i]);
            if ($ansDetail || Utility::getImage('answer', $quizDetail->quiz_id, $i, true)) {
                $listAns[] = [
                    'ans_id' => $i,
                    'content' => ($ansDetail) ? $ansDetail->content : '',
                    'img_ans' => Utility::getImage('answer', $quizDetail->quiz_id, $i, true)
                ];
            }
        }
        return [
            'status' => 200,
            'data' => [
                'quiz_id' => $quizDetail->quiz_id,
                'question' => $quizDetail->question,
                'quiz_number' => $quizDetail->quiz_number,
                'quiz_year' => $quizDetail->quiz_year,
                'category_main_id' => $quizDetail->category_main_id,
                'category_a_id' => $quizDetail->category_a_id,
                'category_b_id' => $quizDetail->category_b_id,
                'img_question' => Utility::getImage('question', $quizDetail->quiz_id, null, true),
                'listAns' => $listAns,
                'list_ans_two_last' => $this->renderListAnsHistory($quizDetail->quiz_id),
                'total_ans_correct' => count(Utility::exportQuizAnswer($quizDetail->quiz_answer))
            ]
        ];
    }
    
    /*
     * Quiz history ans
     * 
     * Auth : 
     * Create : 17-03-2016
     */
    
    public function actionHistoryAns()
    {
        $modelMemberQuizHistory = new MemberQuizHistory();
        $listAns = $modelMemberQuizHistory->getListAnsForMember();
        if (count($listAns) == 0) {
            return [
                'status' => 204,
                'message' => \Yii::t('app', 'data not found')
            ];
        }
        
        //return data
        $data = [];
        foreach ($listAns as $key => $value) {
            $data[] = [
                'category_main_id' => $value['category_main_id'],
                'category_main_name' => Category::getDetailNameCategory($value['category_main_id']),
                'sub_cat' => Quiz::renderListSubCat($value['category_a_id'], $value['category_b_id']),
                'quiz_id' => (int)$value['quiz_id'],
                'question' => $value['question'],
                'img_question' => Utility::getImage('question', $value['quiz_id'], null, true),
                'list_ans' => $this->renderListAnsHistory($value['quiz_id'])
            ];
        }
        
        return [
            'status' => 200,
            'data' => $data
        ];
    }
    
    /*
     * Quiz history ans
     * 
     * Auth : 
     * Create : 17-03-2016
     */
    
    public function actionHistoryAnsWrong()
    {
        $modelMemberQuizHistory = new MemberQuizHistory();
        $listAnsWrong = $modelMemberQuizHistory->getListAnsWrongForMember();
        if (count($listAnsWrong) == 0) {
            return [
                'status' => 204,
                'message' => \Yii::t('app', 'data not found')
            ];
        }
        
        //return data
        $data = [];
        foreach ($listAnsWrong as $key => $value) {
            $data[] = [
                'category_main_id' => $value['category_main_id'],
                'category_main_name' => Category::getDetailNameCategory($value['category_main_id']),
                'sub_cat' => Quiz::renderListSubCat($value['category_a_id'], $value['category_b_id']),
                'quiz_id' => (int)$value['quiz_id'],
                'question' => $value['question'],
                'img_question' => Utility::getImage('question', $value['quiz_id'], null, true),
                'list_ans' => $this->renderListAnsHistory($value['quiz_id'])
            ];
        }
        
        return [
            'status' => 200,
            'data' => $data
        ];
    }
    
    /*
     * api ans
     * 
     * Auth : 
     * Create : 17-03-2016
     */
    
    public function actionAns()
    {
        $request = Yii::$app->request;
        $dataPost = $request->post();
        
        $modelAns = new Ans();
        $modelAns->setAttributes($dataPost);
        if (!$modelAns->validate()) {
            return [
                    'status' => 400,
                    'messages' => $modelAns->errors
                ];
        }
        $dataSave = $modelAns->saveAns();
        if (!$dataSave) {
            throw new \yii\base\Exception( "System error" );
        }
        
        return  [
            'status' => 200,
            'data' => [
                'flag_ans' => $dataSave
            ]
        ];
    }
    
    /*
     * render history search
     * 
     * Auth : 
     * Created : 05-04-2017
     */
    
    public static function renderHistorySearch($data){
        $dataReturn = [];
        $data = json_decode($data);
        if (count($data) > 0) {
            foreach ($data as $key => $value) {
                $dataReturn[] = [
                    'id' => (int)$value,
                    'name' => Category::getDetailNameCategory($value)
                ];
            }
        }
        return $dataReturn;
    }
    
    /*
     * Render param search
     * 
     * Auth : 
     * Created : 10-04-2017
     */
    public static function renderParamSearch($param) {
        $category = [];
        if (!empty($param['category_main_search']) && count($param['category_main_search'])) {
            foreach ($param['category_main_search'] as $key => $value) {
                if (!empty($param['category_a_search']) && count($param['category_a_search'])) {
                    foreach ($param['category_a_search'] as $key1 => $value1) {
                        $subA = Category::findOne(['parent_id' => $value, 'cateory_id' => $value1]);
                        if ($subA) {
                            $category[] = [
                                'main-id' => (int)$value,
                                'sub-a' => (int)$value1,
                                'sub-b' => (!empty($param['category_b_search'])) ? self::renderParamSubSearch($param['category_b_search'], $value1) : []
                            ];
                        } else {
                            $category[] = [
                                'main-id' => $value
                            ];
                        }
                    }
                } else {
                    $category[] = [
                        'main-id' => $value
                    ];
                }
            }
        }
        return $category;
        
    }
    
    public static function renderParamSubSearch($param, $valueParent) {
        $data = [];
        if (count($param) > 0) {
            foreach ($param as $key => $value) {
                $subA = Category::findOne(['parent_id' => $valueParent, 'cateory_id' => $value]);
                if ($subA) {
                    $data[] = (int)$value;
                }
            }
        }
        $dataReturn = null;
        if (count($data) > 0) {
            $dataReturn .= '(';
            foreach ($data as $key => $value) {
                if ($key != count($data) -1) {
                    $dataReturn .= $value . ',';
                } else {
                    $dataReturn .= $value;
                }
            }
            $dataReturn .= ')';
        }
        return $dataReturn;
    }
    
    /*
     * Add question
     * 
     * Auth :
     * Created : 23-04-2017
     */
    
    public function actionAddQuestion() {
        $request = Yii::$app->request;
        $dataPost = $request->post();
        $modelQuestion = new Question();
        $fileUpload = $_FILES;
        $modelQuestion->setAttributes($dataPost);
        $modelQuestion->scenario  = Question::SCENARIO_ADD_QUIZ;
        //check validate
        if ($modelQuestion->validate() && $modelQuestion->validateAnswer($fileUpload)
                && $modelQuestion->validateExtensions($fileUpload) && $modelQuestion->validateRequireAnswer($fileUpload)) {
            //update data
            $quiz_id = $modelQuestion->saveQuestion($fileUpload);
            if (!$quiz_id) {
                throw new \yii\base\Exception( "System error" );

            }
            return [
                'status' => 200,
                'data' => [
                        'quiz_id' => $quiz_id
                ]
            ];
        } else {
            return [
                'status' => 400,
                'messages' => $modelQuestion->errors
            ];
        }
        
    }
}