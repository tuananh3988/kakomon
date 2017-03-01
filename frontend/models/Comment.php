<?php

namespace frontend\models;

use Yii;
use yii\base\Model;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use common\models\Activity;
use common\models\Quiz;
/**
 * ContactForm is the model behind the contact form.
 */
class Comment extends \yii\db\ActiveRecord
{
    
    const SCENARIO_ADD_COMMENT = 'add';
    const SCENARIO_DELETE_COMMENT = 'delete';
    
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'activity';
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['quiz_id', 'content'], 'required', 'on' => self::SCENARIO_ADD_COMMENT],
            [['quiz_id'], 'integer', 'on' => self::SCENARIO_ADD_COMMENT],
            ['quiz_id', 'validateQuizId', 'on' => self::SCENARIO_ADD_COMMENT],
            [['activity_id'], 'required', 'on' => self::SCENARIO_DELETE_COMMENT],
            ['activity_id', 'validateActivityId', 'on' => self::SCENARIO_DELETE_COMMENT],
            [['content', 'quiz_id', 'activity_id', 'created_date', 'updated_date'], 'safe'],
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
    public function attributeLabels()
    {
        return [
            'activity_id' => 'Activity ID',
            'member_id' => 'Member ID',
            'type' => 'Type',
            'status' => 'Status',
            'quiz_id' => 'Quiz ID',
            'relate_id' => 'Relate ID',
            'content' => 'Content',
            'created_date' => 'Created Date',
            'updated_date' => 'Updated Date',
        ];
    }
    
    /*
     * Validate quiz id
     * 
     * Auth :
     * Create : 01-03-2017
     * 
     */
    
    public function validateQuizId($attribute)
    {
        if (!$this->hasErrors()) {
            $quizDetail = Quiz::findOne(['quiz_id' => $this->$attribute, 'type' => Quiz::TYPE_DEFAULT]);
            if (!$quizDetail) {
                $this->addError($attribute, \Yii::t('app', 'data not exist', ['attribute' => $this->attributeLabels()[$attribute]]));
            }
        }
    }
    
    public function validateActivityId($attribute)
    {
        if (!$this->hasErrors()) {
            $activity = Activity::findOne(['activity_id' => $this->$attribute, 'type' => Activity::TYPE_COMMENT]);
            if (!$activity) {
                $this->addError($attribute, \Yii::t('app', 'data not exist', ['attribute' => $this->attributeLabels()[$attribute]]));
            }
        }
    }
}