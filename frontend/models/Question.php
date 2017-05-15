<?php

namespace frontend\models;

use Yii;
use yii\base\Model;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use common\models\MemberQuizHistory;
use common\models\Quiz;
use common\models\MemberQuizSearchHistory;
use common\models\MemberQuizActivity;
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

    const TYPE_ALL = 1;
    const TYPE_RIGHT = 2;
    const TYPE_WRONG = 3;
    const TYPE_DO_NOT = 4;
    
    const SCENARIO_LIST_QUIZ = 'list';
    const SCENARIO_DETAIL_QUIZ = 'detail';
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
        return ['category_main_search', 'category_a_search', 'category_b_search', 'quiz_year_search'];
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
                            $sql .= ' WHERE `quiz`.`quiz_id` NOT IN '. $listQuiz;
                        }
                        break;
                default :
            }
            if ($type == 4) {
                $sql .= ' AND `quiz`.`delete_flag` = '. Quiz::QUIZ_ACTIVE;
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
}