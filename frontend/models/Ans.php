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
            [['quiz_id'], 'required'],
            [['quiz_id'], 'integer'],
            ['quiz_id', 'validateQuizId'],
            [['quiz_id' , 'quiz_answer1', 'quiz_answer2', 'quiz_answer3', 'quiz_answer4',
                'quiz_answer5', 'quiz_answer6', 'quiz_answer7', 'quiz_answer8', 'created_date', 'updated_date'], 'safe'],
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
                $nameAns = 'quiz_answer'.$i;
                $data['Quiz']['quiz_answer'.$i] = $this->$nameAns;
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
            $modelMemberQuizHistory->save();
            
            //insert data table member_quiz_activity if not found record
            $quizActivityDetail = MemberQuizActivity::findOne(['member_id' => Yii::$app->user->identity->member_id, 'quiz_id' => $this->quiz_id]);
            if (!$quizActivityDetail && ($quizAnswer != Quiz::QUIZ_ANSWER)){
                $modelMemberQuizActivity = new MemberQuizActivity();
                $modelMemberQuizActivity->member_id = Yii::$app->user->identity->member_id;
                $modelMemberQuizActivity->quiz_id = $this->quiz_id;
                $modelMemberQuizActivity->save();
            }
            $transaction->commit();
            return TRUE;
        } catch (Exception $ex) {
            $transaction->rollBack();
            return FALSE;
        }
    }
}