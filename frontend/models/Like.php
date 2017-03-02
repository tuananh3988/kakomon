<?php

namespace frontend\models;

use Yii;
use yii\base\Model;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use common\models\Activity;
/**
 * ContactForm is the model behind the contact form.
 */
class Like extends \yii\db\ActiveRecord
{
    
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
            [['activity_id', 'status'], 'required'],
            [['activity_id', 'status'], 'integer'],
            ['activity_id', 'validateActivityId'],
            [['created_date', 'updated_date'], 'safe'],
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
    
    public function validateActivityId($attribute)
    {
        if (!$this->hasErrors()) {
            $activity = Activity::findOne(['activity_id' => $this->$attribute]);
            if (!$activity) {
                $this->addError($attribute, \Yii::t('app', 'data not exist', ['attribute' => $this->attributeLabels()[$attribute]]));
            }
        }
    }
    
    /*
     * check like
     * 
     * Auth : 
     * Created : 02-03-2017
     */
    public static function checkLikeByActivityId($activityId, $memberId)
    {
        $idLike  = Activity::findOne(['relate_id' => $activityId, 'member_id' => $memberId, 'type' => Activity::TYPE_LIKE, 'status' => Activity::STATUS_ACTIVE]);
        if (!$idLike) {
            return false;
        }
        return true;
    }
    
    /*
     * check dislike
     * 
     * Auth : 
     * Created : 02-03-2017
     */
    public static function checkDisLikeByActivityId($activityId, $memberId)
    {
        $idDisLike  = Activity::findOne(['relate_id' => $activityId, 'member_id' => $memberId, 'type' => Activity::TYPE_DISLIKE, 'status' => Activity::STATUS_ACTIVE]);
        if (!$idDisLike) {
            return false;
        }
        return true;
    }
    
    
    /*
     * get total Like by activity
     * 
     * Auth : 
     * Created : 02-03-2017
     */
    public static function getTotalLikeByActivityId($activityId)
    {
        $query = new \yii\db\Query();
        $query->select(['activity.activity_id'])
                ->from('activity');
        $query->where(['=', 'activity.relate_id', $activityId]);
        $query->andWhere(['=', 'activity.type', Activity::TYPE_LIKE]);
        $query->andWhere(['=', 'activity.status', Activity::STATUS_ACTIVE]);
        return $query->count();
    }
    
    /*
     * get total DisLike by activity
     * 
     * Auth : 
     * Created : 02-03-2017
     */
    public static function getTotalDisLikeByActivityId($activityId)
    {
        $query = new \yii\db\Query();
        $query->select(['activity.activity_id'])
                ->from('activity');
        $query->where(['=', 'activity.relate_id', $activityId]);
        $query->andWhere(['=', 'activity.type', Activity::TYPE_DISLIKE]);
        $query->andWhere(['=', 'activity.status', Activity::STATUS_ACTIVE]);
        return $query->count();
    }
}