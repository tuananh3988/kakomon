<?php

namespace frontend\models;

use Yii;
use yii\base\Model;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use common\models\MemberQuizHistory;
use common\models\Quiz;
/**
 * ContactForm is the model behind the contact form.
 */
class Question extends \yii\db\ActiveRecord
{
    public $type_quiz;


    const TYPE_ALL = 1;
    const TYPE_RIGHT = 2;
    const TYPE_WRONG = 3;
    const TYPE_DO_NOT = 4;
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
            [['quiz_class'], 'required'],
            [['quiz_class', 'category_main_id', 'category_a_id', 'category_b_id', 'quiz_year', 'type_quiz', 'created_date', 'updated_date'], 'safe'],
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
     * Get list question
     * 
     * Auth : 
     * Create : 15-03-2017
     */
    
    public function getListQuiz($limit = null, $offset = null , $flag = false){
        $type = ($this->type_quiz) ? $this->type_quiz : self::TYPE_ALL;
        $query = new \yii\db\Query();
        $query->select(['quiz.*'])
                ->from('quiz');
        $query->where(['=', 'quiz.delete_flag', Quiz::QUIZ_ACTIVE]);
        $query->andFilterWhere(['=', 'quiz.category_main_id', $this->category_main_id]);
        $query->andFilterWhere(['=', 'quiz.category_a_id', $this->category_a_id]);
        $query->andFilterWhere(['=', 'quiz.category_b_id', $this->category_b_id]);
        $query->andFilterWhere(['=', 'quiz.quiz_year', $this->quiz_year]);
        switch ($type) {
            case 1:
                break;
            case 2:
                $query->join('INNER JOIN', 'member_quiz_history', 'quiz.quiz_id = member_quiz_history.quiz_id');
                $query->andFilterWhere(['=', 'member_quiz_history.member_id', Yii::$app->user->identity->member_id]);
                $query->andFilterWhere(['=', 'member_quiz_history.correct_flag', MemberQuizHistory::FLAG_CORRECT_CORRECT]);
                $query->andFilterWhere(['=', 'member_quiz_history.last_ans_flag', MemberQuizHistory::FLAG_ANS_LAST]);
                break;
            case 3:
                $query->join('INNER JOIN', 'member_quiz_history', 'quiz.quiz_id = member_quiz_history.quiz_id');
                $query->andFilterWhere(['=', 'member_quiz_history.member_id', Yii::$app->user->identity->member_id]);
                $query->andFilterWhere(['=', 'member_quiz_history.correct_flag', MemberQuizHistory::FLAG_CORRECT_INCORRECT]);
                $query->andFilterWhere(['=', 'member_quiz_history.last_ans_flag', MemberQuizHistory::FLAG_ANS_LAST]);
                break;
            case 4:
                $query->join('INNER JOIN', 'member_quiz_history', 'quiz.quiz_id = member_quiz_history.quiz_id');
                $query->andFilterWhere(['=', 'member_quiz_history.member_id', Yii::$app->user->identity->member_id]);
                $query->andFilterWhere(['=', 'member_quiz_history.correct_flag', MemberQuizHistory::FLAG_CORRECT_NOT_DOING]);
                $query->andFilterWhere(['=', 'member_quiz_history.last_ans_flag', MemberQuizHistory::FLAG_ANS_LAST]);
                break;
            default :
                
        }
        if ($flag) {
            return $query->count();
        }
        $query->offset($offset);
        $query->limit($limit);
        return $query->all();
    }
}