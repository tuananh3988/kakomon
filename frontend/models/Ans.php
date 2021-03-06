<?php

namespace frontend\models;

use Yii;
use yii\base\Model;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use common\models\Activity;
use common\models\Quiz;
use common\components\Utility;
use common\models\MemberQuizHistory;
use common\models\MemberQuizActivity;
use common\models\MemberCategoryTime;
/**
 * ContactForm is the model behind the contact form.
 */
class Ans extends \yii\db\ActiveRecord
{
    public $quiz_answer1;
    public $quiz_answer2;
    public $quiz_answer3;
    public $quiz_answer4;
    public $quiz_answer5;
    public $quiz_answer6;
    public $quiz_answer7;
    public $quiz_answer8;
    public $quiz_answer;
    public $exam_id;
    public $type_ans;
    public $contest_times;


    const TYPE_ANS_DEFAULT = 1;
    const TYPE_ANS_QUICK_QUIZ = 2;
    const TYPE_ANS_EXAM = 3;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'member_quiz_history';
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['quiz_id', 'time', 'type_ans'], 'required'],
            [['quiz_id', 'time', 'type_ans'], 'integer'],
            [['quiz_answer'], 'each', 'rule' => ['integer']],
            ['type_ans', 'validateAnsExam'],
            ['quiz_id', 'validateQuizId'],
            [['quiz_id' , 'quiz_answer', 'quiz_answer1', 'quiz_answer2', 'quiz_answer3', 'quiz_answer4', 'time', 'exam_id', 'type_ans',
                'quiz_answer5', 'quiz_answer6', 'quiz_answer7', 'quiz_answer8', 'created_date', 'updated_date', 'contest_times'], 'safe'],
        ];
    }

    public function __construct() {
        $this->quiz_answer = [];
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
            'quiz_answer' => 'Quiz Answer',
            'staff_create' => 'Staff Create',
            'delete_flag' => 'Delete Flag',
            'created_date' => 'Created Date',
            'updated_date' => 'Updated Date',
            'question_img' => 'Question Img',
            'remove_img_question_flg' => 'remove_img_question_flg',
            'quiz_answer' => 'Quiz Answer',
            'quiz_answer1' => 'Quiz Answer1',
            'quiz_answer2' => 'Quiz Answer2',
            'quiz_answer3' => 'Quiz Answer3',
            'quiz_answer4' => 'Quiz Answer4',
            'quiz_answer5' => 'Quiz Answer5',
            'quiz_answer6' => 'Quiz Answer6',
            'quiz_answer7' => 'Quiz Answer7',
            'quiz_answer8' => 'Quiz Answer8',
            'time' => 'Time'
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
    
    /*
     * Validate quiz id
     * 
     * Auth :
     * Create : 08-03-2017
     * 
     */
    
    public function validateQuizId($attribute)
    {
        if (!$this->hasErrors()) {
            $quizDetail = Quiz::findOne(['quiz_id' => $this->$attribute, 'delete_flag' => Quiz::QUIZ_ACTIVE, 'type' => Quiz::TYPE_NORMAL]);
            if ($this->type_ans == self::TYPE_ANS_QUICK_QUIZ) {
                $quizDetail = Quiz::findOne(['quiz_id' => $this->$attribute, 'delete_flag' => Quiz::QUIZ_ACTIVE, 'type' => Quiz::TYPE_QUICK_QUIZ]);
            }
            if ($this->type_ans == self::TYPE_ANS_EXAM) {
                $quizDetail = Quiz::findOne(['quiz_id' => $this->$attribute, 'delete_flag' => Quiz::QUIZ_ACTIVE, 'type' => Quiz::TYPE_COLLECT]);
            }
            if (!$quizDetail) {
                $this->addError($attribute, \Yii::t('app', 'data not exist', ['attribute' => $this->attributeLabels()[$attribute]]));
            }
        }
    }
    /*
     * Validate ans exam
     * 
     * Auth :
     * Create : 21-06-2017
     * 
     */
    public function validateAnsExam($attribute) {
        if ($this->type_ans == self::TYPE_ANS_EXAM) {
            if (empty($this->exam_id) || empty($this->contest_times)) {
                $this->addError('exam', \Yii::t('app', 'Please input exam_id and contest_times!'));
            }
        }
    }
    /*
     * save ans
     * 
     * Auth : 
     * Create : 18-03-2017
     */
    
    public function saveAns(){
        
        $transaction = \yii::$app->getDb()->beginTransaction();
        try {
            for ($i = 1; $i <= 8; $i++) {
                $data['Quiz']['quiz_answer'.$i] = (in_array($i, $this->quiz_answer)) ? 1 : null;
            }
            $quizDetail = Quiz::findOne(['quiz_id' => $this->quiz_id, 'delete_flag' => Quiz::QUIZ_ACTIVE]);
            $quizAnswer = Utility::renderQuizAnswer($data);
            //update all data table member_quiz_history for quiz_id and member_id
            if ($quizAnswer != Quiz::QUIZ_ANSWER && $this->type_ans !== self::TYPE_ANS_EXAM) {
                MemberQuizHistory::updateAll(['last_ans_flag' => MemberQuizHistory::FLAG_ANS_BEFORE], 'quiz_id = '. $this->quiz_id . ' AND member_id = ' . Yii::$app->user->identity->member_id);
            }
            // case ans exam ==> update all data old
            if ($this->type_ans == self::TYPE_ANS_EXAM) {
                MemberQuizHistory::updateAll(['last_ans_flag' => MemberQuizHistory::FLAG_ANS_BEFORE], 'quiz_id = '. $this->quiz_id . ' AND member_id = ' . Yii::$app->user->identity->member_id . ' AND contest_times = ' . $this->contest_times);
            }
            
            //insert new data table member_quiz_history
            $lastAnsFlag = MemberQuizHistory::FLAG_ANS_LAST;
            if ($quizAnswer == Quiz::QUIZ_ANSWER) {
                $correctFlag = MemberQuizHistory::FLAG_CORRECT_NOT_DOING;
                $lastAnsFlag = MemberQuizHistory::FLAG_ANS_BEFORE;
            } elseif ($quizAnswer == $quizDetail->quiz_answer) {
                $correctFlag = MemberQuizHistory::FLAG_CORRECT_CORRECT;
            } else {
                $correctFlag = MemberQuizHistory::FLAG_CORRECT_INCORRECT;
            }
            //case ans exam 
            if ($this->type_ans == self::TYPE_ANS_EXAM) {$lastAnsFlag = MemberQuizHistory::FLAG_ANS_LAST;}
            
            $modelMemberQuizHistory = new MemberQuizHistory();
            $modelMemberQuizHistory->quiz_id = $this->quiz_id;
            $modelMemberQuizHistory->last_ans_flag = $lastAnsFlag;
            $modelMemberQuizHistory->member_id = Yii::$app->user->identity->member_id;
            $modelMemberQuizHistory->answer = $quizAnswer;
            $modelMemberQuizHistory->correct_flag = $correctFlag;
            $modelMemberQuizHistory->time = $this->time;
            $modelMemberQuizHistory->exam_id = $this->exam_id;
            $modelMemberQuizHistory->contest_times = ($this->type_ans == self::TYPE_ANS_EXAM) ? ($this->contest_times) : null;
            $modelMemberQuizHistory->save();
            
            //update or inset table member_category_time
            if ($this->type_ans != self::TYPE_ANS_EXAM) {
                $memberCategoryTime = MemberCategoryTime::findOne(['member_id' => Yii::$app->user->identity->member_id, 'category_id' => $quizDetail->category_main_id]);
                if (!$memberCategoryTime) {
                    $modelMemberCategoryTime = new MemberCategoryTime();
                    $modelMemberCategoryTime->member_id = Yii::$app->user->identity->member_id;
                    $modelMemberCategoryTime->category_id = $quizDetail->category_main_id;
                    $modelMemberCategoryTime->total_time = $this->time;
                    $modelMemberCategoryTime->save();
                } else {
                    $memberCategoryTime->total_time = $memberCategoryTime->total_time + $this->time;
                    $memberCategoryTime->save();
                }
            }
            
            $transaction->commit();
            return $correctFlag;
        } catch (Exception $ex) {
            $transaction->rollBack();
            return FALSE;
        }
    }
}