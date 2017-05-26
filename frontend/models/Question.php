<?php

namespace frontend\models;

use Yii;
use yii\base\Model;
use yii\web\UploadedFile;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use common\models\MemberQuizHistory;
use common\models\Quiz;
use common\models\Answer;
use common\models\MemberQuizSearchHistory;
use common\models\MemberQuizActivity;
use common\components\Utility;
/**
 * ContactForm is the model behind the contact form.
 */
class Question extends \yii\db\ActiveRecord
{
    public $type_quiz;
    public $category_main_search;
    public $category_a_search;
    public $category_b_search;
    public $quiz_year_search;
    
    public $question;
    public $category_main_id;
    public $category_a_id;
    public $category_b_id;
    public $answer1_content;
    public $answer2_content;
    public $answer3_content;
    public $answer4_content;
    public $answer5_content;
    public $answer6_content;
    public $answer7_content;
    public $answer8_content;
    public $quiz_answer;
    public $quiz_answer1;
    public $quiz_answer2;
    public $quiz_answer3;
    public $quiz_answer4;
    public $quiz_answer5;
    public $quiz_answer6;
    public $quiz_answer7;
    public $quiz_answer8;
    public $quiz_number;
    public $quiz_year;
    public $test_times;
    
    //images
    public $question_img;
    public $answer1_img;
    public $answer2_img;
    public $answer3_img;
    public $answer4_img;
    public $answer5_img;
    public $answer6_img;
    public $answer7_img;
    public $answer8_img;

    const TYPE_ALL = 1;
    const TYPE_RIGHT = 2;
    const TYPE_WRONG = 3;
    const TYPE_DO_NOT = 4;
    
    const SCENARIO_LIST_QUIZ = 'list';
    const SCENARIO_DETAIL_QUIZ = 'detail';
    const SCENARIO_ADD_QUIZ = 'add';
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'quiz';
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['quiz_class'], 'required', 'on' => self::SCENARIO_LIST_QUIZ],
            [['category_main_search', 'category_a_search', 'category_b_search', 'quiz_year_search'], 'validateType', 'on' => self::SCENARIO_LIST_QUIZ],
            [['quiz_id', 'type'], 'required', 'on' => self::SCENARIO_DETAIL_QUIZ],
            [['quiz_class', 'quiz_id', 'type', 'category_main_id', 'category_a_id', 'category_b_id', 'quiz_year', 'type_quiz', 'created_date', 'updated_date'], 'safe'],
            //add question
            [['question'], 'required', 'on' => self::SCENARIO_ADD_QUIZ],
            [['type', 'category_main_id', 'category_a_id', 'category_b_id', 'quiz_year', 'test_times', 'quiz_number'], 'integer',  'on' => self::SCENARIO_ADD_QUIZ],
        ];
    }
    
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                          ActiveRecord::EVENT_BEFORE_INSERT => ['created_date'],
                          ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_date'],
                ],
                'value' => date('Y-m-d H:i:s'),
            ],
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function safeAttributes()
    {
        $safe = parent::safeAttributes();
        return array_merge($safe, $this->extraFields());
    }
    
    /**
     * @inheritdoc
     */
    public function extraFields()
    {
        return ['category_main_search', 'category_a_search', 'category_b_search', 'quiz_year_search', 'question', 'category_main_id',
            'category_a_id', 'category_b_id', 'answer1_content', 'answer2_content', 'answer3_content', 'answer4_content', 'answer5_content',
            'answer6_content', 'answer7_content', 'answer8_content', 'quiz_answer1', 'quiz_answer2', 'quiz_answer3', 'quiz_answer4', 'quiz_answer5',
            'quiz_answer6', 'quiz_answer7', 'quiz_answer8', 'quiz_number', 'quiz_number', 'quiz_year', 'quiz_year', 'test_times', 'quiz_answer'];
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'quiz_id' => 'Quiz ID',
            'type' => 'Type',
            'question' => 'Question',
            'category_main_id' => 'Category Id 1',
            'category_a_id' => 'Category Id 2',
            'category_b_id' => 'Category Id 3',
            'answer_id' => 'Answer',
            'quiz_year' => 'Quiz Year',
            'quiz_number' => 'Quiz Number',
            'quiz_answer' => 'Quiz Answer',
            'test_times' => 'Test Times',
            'staff_create' => 'Staff Create',
            'delete_flag' => 'Delete Flag',
            'created_date' => 'Created Date',
            'updated_date' => 'Updated Date',
            'question_img' => 'Question Img',
            'quiz_answer1' => 'Quiz Answer1',
            'quiz_answer2' => 'Quiz Answer2',
            'quiz_answer3' => 'Quiz Answer3',
            'quiz_answer4' => 'Quiz Answer4',
            'quiz_answer5' => 'Quiz Answer5',
            'quiz_answer6' => 'Quiz Answer6',
            'quiz_answer7' => 'Quiz Answer7',
            'quiz_answer8' => 'Quiz Answer8',
        ];
    }
    
     /*
     * validate unique mail
     * 
     * Auth : 
     * Create : 03-01-2017
     */
    public function validateType($attribute)
    {
        if (!$this->hasErrors()) {
            if (!is_array($this->$attribute)) {
                $this->addError($attribute, 'Data must be an array');
            }
        }
    }
    
    /*
     * Validate Answer
     * 
     * Auth :
     * Create : 26/05/2017
     */
    public function validateAnswer($fileUpload)
    {
        for($i = 1 ; $i <= Quiz::MAX_ANS; $i++) {
            $keyAnswer = 'answer' . $i . '_content';
            $keyAnswerImg = 'answer' . $i . '_img';
            $keyQuizAnswer = 'quiz_answer' . $i;
            if (($this->$keyQuizAnswer == 1) && (empty($this->$keyAnswer)) && (count($fileUpload) > 0 && empty($fileUpload[$keyAnswerImg]) || count($fileUpload) == 0)) {
                $this->addError('answer', \Yii::t('app', 'Answer not map!'));
                return FALSE;
            }
        }
        return TRUE;
    }
    
    /*
     * Validate require answer
     * 
     * Auth :
     * Created : 26/05/2017
     */
    public function validateRequireAnswer($fileUpload)
    {
        $flag = FALSE;
        for($i = 1 ; $i <= Quiz::MAX_ANS; $i++) {
            $keyQuizAnswer = 'quiz_answer' . $i;
            if ($this->$keyQuizAnswer == 1) {
                $flag = TRUE;
                break;
            }
        }
        if ($flag) {
            return TRUE;
        } else {
            $this->addError('answer', \Yii::t('app', 'Answer require!'));
            return FALSE;
        }
    }
    
    /*
     * Validate extension file
     * 
     * Auth :
     * Create : 15-02-2017
     */
    public function validateExtensions($fileUpload){
        if ((count($fileUpload) > 0 && !empty($fileUpload['question_img'])) && !in_array(pathinfo($fileUpload['question_img']['name'], PATHINFO_EXTENSION), ['png', 'jpg', 'jpeg'])) {
            $this->addError('question_img', \Yii::t('app', 'Only files with these extensions are allowed: png, jpg, jpeg.!'));
            return FALSE;
        }
        for($i = 1 ; $i <= Quiz::MAX_ANS; $i++) {
            $keyAnswerImg = 'answer' . $i . '_img';
            if ((count($fileUpload) > 0 && !empty($fileUpload[$keyAnswerImg])) && !in_array(pathinfo($fileUpload[$keyAnswerImg]['name'], PATHINFO_EXTENSION), ['png', 'jpg', 'jpeg'])) {
                $this->addError($keyAnswerImg, \Yii::t('app', 'Only files with these extensions are allowed: png, jpg, jpeg.!'));
                return FALSE;
            }
        }
        return TRUE;
    }
    
    /*
     * Get list question
     * 
     * Auth : 
     * Create : 15-03-2017
     */
    
    public function getListQuiz($paramSearch, $limit = null, $offset = null , $flag = false){
        $connection = Yii::$app->getDb();
        $sql = '';
        $type = ($this->type_quiz) ? $this->type_quiz : self::TYPE_ALL;
        foreach ($paramSearch as $key => $value) {
            $sql .= ' SELECT quiz.* FROM quiz';
            switch ($type) {
                case 1:
                        break;
                case 2:
                        $sql .= ' INNER JOIN `member_quiz_history` ON quiz.quiz_id = member_quiz_history.quiz_id'
                        . ' AND `member_quiz_history`.`member_id` = ' . Yii::$app->user->identity->member_id . ' AND `member_quiz_history`.`correct_flag` = '. MemberQuizHistory::FLAG_CORRECT_CORRECT . ' AND `member_quiz_history`.`last_ans_flag` = '. MemberQuizHistory::FLAG_ANS_LAST;
                        break;
                case 3:
                        $sql .= ' INNER JOIN `member_quiz_history` ON quiz.quiz_id = member_quiz_history.quiz_id'
                        . ' AND `member_quiz_history`.`member_id` = ' . Yii::$app->user->identity->member_id . ' AND `member_quiz_history`.`correct_flag` = '. MemberQuizHistory::FLAG_CORRECT_INCORRECT . ' AND `member_quiz_history`.`last_ans_flag` = '. MemberQuizHistory::FLAG_ANS_LAST;
                        break;
                case 4:
                        $listQuiz = $this->getListQuizCorrectAndIncorrect();
                        if (!is_null($listQuiz)) {
                            $sql .= ' WHERE `quiz`.`quiz_id` NOT IN '. $listQuiz . ' AND ';
                        } else {
                            $sql .= ' WHERE ';
                        }
                        break;
                default :
            }
            if ($type == 4) {
                $sql .= ' `quiz`.`delete_flag` = '. Quiz::QUIZ_ACTIVE;
            } else {
                $sql .= ' WHERE `quiz`.`delete_flag` = '. Quiz::QUIZ_ACTIVE;
            }
            $sql .= ' AND `quiz`.`type` = '. Quiz::TYPE_NORMAL;
            $sql .= ' AND `quiz`.`quiz_class` = '. $this->quiz_class;
            $sql .= ' AND `quiz`.`category_main_id` = '. $value['main-id'];
            if (!empty($value['sub-a'])) {
                $sql .= ' AND `quiz`.`category_a_id` = '. $value['sub-a'];
            }
            if (!empty($value['sub-b']) && !is_null($value['sub-b'])) {
                $sql .= ' AND `quiz`.`category_b_id` IN '. $value['sub-b'];
            }
            if (!empty($this->quiz_year_search) && (count($this->quiz_year_search) > 0)) {
                $sql .= ' AND `quiz`.`quiz_year` IN '. $this->renderString($this->quiz_year_search);
            }
            if ($key != (count($paramSearch) - 1)) {
                $sql .= ' UNION';
            }
        }
        if ($flag) {
            return count($connection->createCommand($sql)->queryAll());
        }
        $sql .= ' LIMIT '. $limit . ' OFFSET ' . $offset;
        return $connection->createCommand($sql)->queryAll();
    }
    
    /*
     * Get list year
     * 
     * Auth : 
     * Create : 16-03-2017
     */
    
    public function getListYear()
    {
        $query = new \yii\db\Query();
        $query->select(['test_times', 'quiz_year'])
                ->from('quiz');
        $query->where(['=', 'quiz.type', Quiz::TYPE_NORMAL]);
        $query->andWhere(['=', 'quiz.delete_flag', Quiz::QUIZ_ACTIVE]);
        $query->andWhere(['not',  ['quiz.quiz_year' => null]]);
        $query->andWhere(['not',  ['quiz.test_times' => null]]);
        $query->distinct();
        $query->orderBy(['quiz_year' => SORT_DESC]);
        return $query->all();
    }
    
    /*
     * Insert history search
     * 
     * Auth : 
     * Create : 16-03-2017
     */
    
    public function insertHistorySearch(){
        $type = ($this->type_quiz) ? $this->type_quiz : self::TYPE_ALL;
        $categoryMainSearch = ($this->category_main_search) ? json_encode($this->category_main_search) : json_encode([]);
        $categoryASearch = ($this->category_a_search) ? json_encode($this->category_a_search) : json_encode([]);
        $categoryBSearch = ($this->category_b_search) ? json_encode($this->category_b_search) : json_encode([]);
        $quizYearSearch = ($this->quiz_year_search) ? json_encode($this->quiz_year_search) : json_encode([]);
        $quizSearchHistory = new MemberQuizSearchHistory();
        $quizSearchHistory->member_id = Yii::$app->user->identity->member_id;
        $quizSearchHistory->quiz_class = $this->quiz_class;
        $quizSearchHistory->category_main_id = $categoryMainSearch;
        $quizSearchHistory->category_a_id = $categoryASearch;
        $quizSearchHistory->category_b_id = $categoryBSearch;
        $quizSearchHistory->quiz_year = $quizYearSearch;
        $quizSearchHistory->type = $type;
        $quizSearchHistory->save();
    }
    
    /*
     * get list quiz CORRECT and INCORRECT
     */
    public static function getListQuizCorrectAndIncorrect(){
        
        $correct = MemberQuizHistory::find()->select('quiz_id')->where(['member_id' => Yii::$app->user->identity->member_id, 'correct_flag' => MemberQuizHistory::FLAG_CORRECT_CORRECT, 'last_ans_flag' => MemberQuizHistory::FLAG_ANS_LAST])->asArray()->all();
        $incorrect = MemberQuizHistory::find()->select('quiz_id')->where(['member_id' => Yii::$app->user->identity->member_id, 'correct_flag' => MemberQuizHistory::FLAG_CORRECT_INCORRECT, 'last_ans_flag' => MemberQuizHistory::FLAG_ANS_LAST])->asArray()->all();
        $data = array_merge($correct, $incorrect);
        $dataReturn = null;
        if (count($data) > 0) {
            $dataReturn .= '(';
            foreach ($data as $key => $value) {
                if ($key != count($data) -1) {
                    $dataReturn .= $value['quiz_id'] . ',';
                } else {
                    $dataReturn .= $value['quiz_id'];
                }
            }
            $dataReturn .= ')';
        }
        return $dataReturn;
    }
    
    /*
     * conver string from array
     * 
     * Auth : 
     * Created : 11-04-2017
     */
    public static function renderString($data){
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
     * save question
     * 
     * Auth : 
     * Created : 25/05/2017
     */
    public function saveQuestion($fileUpload){
        $transaction = \yii::$app->getDb()->beginTransaction();
        try {
            $utility = new Utility();
            $quiz = new Quiz();
            $quiz->type = Quiz::TYPE_COLLECT;
            $quiz->question = $this->question;
            $quiz->category_main_id = $this->category_main_id;
            $quiz->category_a_id = $this->category_a_id;
            $quiz->category_b_id = $this->category_b_id;
            $quiz->quiz_year = $this->quiz_year;
            $quiz->test_times = $this->test_times;
            $quiz->quiz_number = $this->quiz_number;
            $quiz->quiz_answer = $this->renderQuizAnswerForApi();
            $quiz->staff_create = Yii::$app->user->identity->member_id;
            $quiz->save();
            //upload image question
            
            if (count($fileUpload) > 0 && !empty($fileUpload['question_img'])) {
                $utility->uploadImagesQuizForApi($fileUpload, 'question', 'question_img' , $quiz->quiz_id);
            }
            
            for($i = 1 ; $i <= Quiz::MAX_ANS; $i++) {
                $keyAnswer = 'answer' . $i . '_content';
                $keyAnswerImg = 'answer' . $i . '_img';
                if (($this->$keyAnswer) || (count($fileUpload) > 0 && !empty($fileUpload[$keyAnswerImg]))) {
                    $modelAnswer = new Answer();
                    $modelAnswer->quiz_id = $quiz->quiz_id;
                    $modelAnswer->content = $this->$keyAnswer;
                    $modelAnswer->order = $i;
                    $modelAnswer->save();
                }
                //upload images ans
                if (count($fileUpload) > 0 && !empty($fileUpload[$keyAnswerImg])) {
                    $utility->uploadImagesQuizForApi($fileUpload, 'answer', $keyAnswerImg , $quiz->quiz_id, $i);
                }
            }
            $transaction->commit();
            return $quiz->quiz_id;
        } catch (Exception $ex) {
            $transaction->rollBack();
            return false;
        }
    }
    
    /*
     * render quiz answer
     * 
     * Auth : 
     * Create : 28-02-2017
     */
    
    public function renderQuizAnswerForApi()
    {
        $dataQuizAnswer = Quiz::QUIZ_ANSWER;
        for ($i = 1; $i <= 8; $i++) {
            $keyquiz_answer = 'quiz_answer' . $i;
            if ($this->$keyquiz_answer == 1) {
                $dataQuizAnswer = substr_replace($dataQuizAnswer, '1', ($i-1), 1);
            }
        }
        return $dataQuizAnswer;
    }
}