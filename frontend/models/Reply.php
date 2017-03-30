<?php

namespace frontend\models;

use Yii;
use yii\base\Model;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use common\models\Activity;
use common\models\Quiz;
use common\components\Utility;
use common\models\ActivitySumary;
/**
 * ContactForm is the model behind the contact form.
 */
class Reply extends \yii\db\ActiveRecord
{
    
    const SCENARIO_ADD_REPLY = 'add';
    const SCENARIO_DELETE_REPLY = 'delete';
    const SCENARIO_LIST_REPLY = 'list';
    
    
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
            [['activity_id', 'content'], 'required', 'on' => self::SCENARIO_ADD_REPLY],
            [['activity_id'], 'integer', 'on' => self::SCENARIO_ADD_REPLY],
            ['activity_id', 'validateActivityIdReply', 'on' => self::SCENARIO_ADD_REPLY],
            [['activity_id'], 'required', 'on' => self::SCENARIO_DELETE_REPLY],
            ['activity_id', 'validateActivityId', 'on' => self::SCENARIO_DELETE_REPLY],
            
            [['activity_id'], 'required', 'on' => self::SCENARIO_LIST_REPLY],
            ['activity_id', 'validateActivityIdForList', 'on' => self::SCENARIO_LIST_REPLY],
            
            [['content', 'activity_id', 'created_date', 'updated_date'], 'safe'],
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
     * Validate
     * 
     * Auth :
     * Create : 01-03-2017
     * 
     */
    
    public function validateActivityIdReply($attribute)
    {
        if (!$this->hasErrors()) {
            $activityDetail = Activity::findOne(['activity_id' => $this->$attribute, 'type' => Activity::TYPE_HELP, 'status' => Activity::STATUS_ACTIVE]);
            if (!$activityDetail) {
                $this->addError($attribute, \Yii::t('app', 'data not exist', ['attribute' => $this->attributeLabels()[$attribute]]));
            }
        }
    }
    
    /*
     * Validate
     * 
     * Auth :
     * Create : 01-03-2017
     * 
     */
    
    public function validateActivityId($attribute)
    {
        if (!$this->hasErrors()) {
            $activity = Activity::findOne(['activity_id' => $this->$attribute, 'member_id' => Yii::$app->user->identity->member_id,'type' => Activity::TYPE_REPLY]);
            if (!$activity) {
                $this->addError($attribute, \Yii::t('app', 'data not exist', ['attribute' => $this->attributeLabels()[$attribute]]));
            }
        }
    }
    
    /*
     * Validate
     * 
     * Auth :
     * Create : 01-03-2017
     * 
     */
    
    public function validateActivityIdForList($attribute)
    {
        if (!$this->hasErrors()) {
            $activity = Activity::findOne(['activity_id' => $this->$attribute, 'type' => Activity::TYPE_HELP, 'status' => Activity::STATUS_ACTIVE]);
            if (!$activity) {
                $this->addError($attribute, \Yii::t('app', 'data not exist', ['attribute' => $this->attributeLabels()[$attribute]]));
            }
        }
    }
    
    /*
     * Get total reply by activity
     * 
     * Auth : 
     * Create : 04-03-2017
     */
    public static function getTotalReplyByActivityId($activityId)
    {
        $query = new \yii\db\Query();
        $query->select('activity.activity_id')
                ->from('activity');
        $query->andWhere(['activity.relate_id' => $activityId]);
        $query->andWhere(['activity.type' => Activity::TYPE_REPLY]);
        $query->andWhere(['activity.status' => Activity::STATUS_ACTIVE]);
        return $query->count();
    }
    
    
    /*
     * Get List Reply
     * 
     * Auth : 
     * Creat : 02-03-2017
     */
    
    public static function getListReplyByActivityId($activityId, $limit, $offset)
    {
        $query = new \yii\db\Query();
        $query->select(['activity.*', 'member.member_id AS meberId', 'member.name',
            'activity_sumary_like.total AS total_like', 'activity_sumary_dis_like.total AS total_dislike',
            'activity_is_like.activity_id AS isLike', 'activity_is_dis_like.activity_id AS isDisLike'])
                ->from('activity');
        $query->join('INNER JOIN', 'member', 'member.member_id = activity.member_id');
        $query->join('LEFT JOIN', 'activity_sumary AS activity_sumary_like', 'activity_sumary_like.activity_id = activity.activity_id AND activity_sumary_like.type = '. ActivitySumary::TYPE_LIKE);
        $query->join('LEFT JOIN', 'activity_sumary AS activity_sumary_dis_like', 'activity_sumary_dis_like.activity_id = activity.activity_id AND activity_sumary_dis_like.type = '. ActivitySumary::TYPE_DIS_LIKE);
        $query->join('LEFT JOIN', 'activity AS activity_is_like', 'activity_is_like.relate_id = activity.activity_id AND activity_is_like.member_id = '. Yii::$app->user->identity->member_id . ' AND activity_is_like.type = ' .Activity::TYPE_LIKE . ' AND activity_is_like.status = ' . Activity::STATUS_ACTIVE);
        $query->join('LEFT JOIN', 'activity AS activity_is_dis_like', 'activity_is_dis_like.relate_id = activity.activity_id AND activity_is_dis_like.member_id = '. Yii::$app->user->identity->member_id . ' AND activity_is_dis_like.type = ' .Activity::TYPE_DISLIKE . ' AND activity_is_dis_like.status = ' . Activity::STATUS_ACTIVE);
        $query->andWhere(['activity.relate_id' => $activityId]);
        $query->andWhere(['activity.type' => Activity::TYPE_REPLY]);
        $query->andWhere(['activity.status' => Activity::STATUS_ACTIVE]);
        $query->offset($offset);
        $query->limit($limit);
        $query->orderBy(['activity_id' => SORT_DESC]);
        return $query->all();
    }
    
    
    /*
     * Render List Reply
     * 
     * Auth : 
     * Create : 02-03-2017
     */
    
    public static function renderListReply($activityId, $limit, $offset)
    {
        $listData = [];
        $list = self::getListReplyByActivityId($activityId, $limit, $offset);
        if (count($list) > 0){
            foreach ($list as $key => $value) {
                $listData[] = [
                    'activity_id' => (int)$value['activity_id'],
                    'member_id' => (int)$value['meberId'],
                    'member_name' => $value['name'],
                    'content' => $value['content'],
                    'isDisLike' => ($value['isDisLike']) ? true : false,
                    'isLike' => ($value['isLike']) ? true : false,
                    'total_like' => (int)$value['total_like'],
                    'total_dislike' => (int)$value['total_dislike'],
                    'avatar' => Utility::getImage('member', $value['meberId'], null, true)
                ];
            }
        }
        return $listData;
    }
    
    /*
     * update data delete reply
     * 
     * Auth : 
     * Create : 20-03-2017
     */
    
    public function updateReply($replyDetail, $activityId, $quizId){
        $transaction = \yii::$app->getDb()->beginTransaction();
        try {
            $replyDetail->status = Activity::STATUS_DELETE;
            $replyDetail->save();
            //update activity
            ActivityApi::deleteActivity($activityId, $quizId);
            $transaction->commit();
            return TRUE;
        } catch (Exception $ex) {
            $transaction->rollBack();
            return FALSE;
        }
    }
}