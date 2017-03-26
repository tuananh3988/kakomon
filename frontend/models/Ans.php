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
            [['quiz_id', 'time'], 'required'],
            [['quiz_id', 'time'], 'integer'],
            ['quiz_id', 'validateQuizId'],
            [['quiz_id' , 'quiz_answer', 'quiz_answer1', 'quiz_answer2', 'quiz_answer3', 'quiz_answer4', 'time',
                'quiz_answer5', 'quiz_answer6', 'quiz_answer7', 'quiz_answer8', 'created_date', 'updated_date'], 'safe'],
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
            $quizDetail = Quiz::findOne(['quiz_id' => $this->$attribute, 'type' => Quiz::TYPE_DEFAULT, 'delete_flag' => Quiz::QUIZ_ACTIVE]);
            if (!$quizDetail) {
                $this->addError($attribute, \Yii::t('app', 'data not exist', ['attribute' => $this->attributeLabels()[$attribute]]));
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
            if ($quizAnswer != Quiz::QUIZ_ANSWER) {
                MemberQuizHistory::updateAll(['last_ans_flag' => MemberQuizHistory::FLAG_ANS_BEFORE], 'quiz_id = '. $this->quiz_id . ' AND member_id = ' . Yii::$app->user->identity->member_id);
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
            
            $modelMemberQuizHistory = new MemberQuizHistory();
            $modelMemberQuizHistory->quiz_id = $this->quiz_id;
            $modelMemberQuizHistory->last_ans_flag = $lastAnsFlag;
            $modelMemberQuizHistory->member_id = Yii::$app->user->identity->member_id;
            $modelMemberQuizHistory->answer = $quizAnswer;
            $modelMemberQuizHistory->correct_flag = $correctFlag;
            $modelMemberQuizHistory->time = $this->time;
            $modelMemberQuizHistory->save();
            
            //update or inset table member_category_time
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
            
            $transaction->commit();
            return $correctFlag;
        } catch (Exception $ex) {
            $transaction->rollBack();
            return FALSE;
        }
    }
}