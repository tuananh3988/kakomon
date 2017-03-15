<?php

namespace frontend\models;

use Yii;
use yii\base\Model;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use common\models\Quiz;
use frontend\models\Reply;
/**
 * ContactForm is the model behind the contact form.
 */
class Quiz extends \yii\db\ActiveRecord
{
    public $type_quiz;


    const TYPE_ALL = 1;
    const TYPE_DID = 2;
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
            [['quiz_class', 'content'], 'required'],
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
    
    public function getListQuiz($limit, $offset){
        $query = new \yii\db\Query();
        $query->select(['quiz.*'])
                ->from('quiz');
        $query->where(['=', 'quiz.delete_flag', Quiz::QUIZ_DELETED]);
        
        $query->andWhere(['=', 'activity.type', self::TYPE_COMMENT]);
        $query->andWhere(['=', 'activity.status', self::STATUS_ACTIVE]);
        $query->offset($offset);
        $query->limit($limit);
        return $query->all();
    }
}