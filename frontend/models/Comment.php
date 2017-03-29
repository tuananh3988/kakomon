<?php

namespace frontend\models;

use Yii;
use yii\base\Model;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use common\models\Activity;
use common\models\Quiz;
use frontend\models\Like;
use common\components\Utility;
use common\models\ActivitySumary;
use frontend\models\ActivityApi;
/**
 * ContactForm is the model behind the contact form.
 */
class Comment extends \yii\db\ActiveRecord
{
    
    const SCENARIO_ADD_COMMENT = 'add';
    const SCENARIO_DELETE_COMMENT = 'delete';
    const SCENARIO_LIST_COMMENT = 'list';


    
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
            
            [['quiz_id'], 'required', 'on' => self::SCENARIO_LIST_COMMENT],
            ['quiz_id', 'validateQuizId', 'on' => self::SCENARIO_LIST_COMMENT],
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
    
    /*
     * Get total comment by quizID
     * 
     * Auth : 
     * Create : 02-03-2017
     */
    public static function getTotalCommnetByQuizID($quizId)
    {
        $query = new \yii\db\Query();
        $query->select('activity.activity_id')
                ->from('activity');
        $query->andWhere(['activity.quiz_id' => $quizId]);
        $query->andWhere(['activity.type' => Activity::TYPE_COMMENT]);
        $query->andWhere(['activity.status' => Activity::STATUS_ACTIVE]);
        return $query->count();
    }
    
    /*
     * Get List comment
     * 
     * Auth : 
     * Creat : 02-03-2017
     */
    
    public static function getListCommentByQuizId($quizId, $limit, $offset)
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
        $query->andWhere(['activity.quiz_id' => $quizId]);
        $query->andWhere(['activity.type' => Activity::TYPE_COMMENT]);
        $query->andWhere(['activity.status' => Activity::STATUS_ACTIVE]);
        $query->offset($offset);
        $query->limit($limit);
        $query->orderBy(['activity_id' => SORT_DESC]);
        return $query->all();
    }
    
    /*
     * Render List Comment
     * 
     * Auth : 
     * Create : 02-03-2017
     */
    
    public static function renderListComment($quizId, $limit, $offset)
    {
        $listData = [];
        $list = self::getListCommentByQuizId($quizId, $limit, $offset);
        if (count($list) > 0){
            foreach ($list as $key => $value) {
                $listData[] = [
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
     * update data delete comment
     * 
     * Auth : 
     * Create : 20-03-2017
     */
    
    public function updateComment($commentDetail, $activityId, $quizId){
        $transaction = \yii::$app->getDb()->beginTransaction();
        try {
            $commentDetail->status = Activity::STATUS_DELETE;
            $commentDetail->save();
            ActivityApi::deleteActivity($activityId, $quizId);
            
            $transaction->commit();
            return TRUE;
        } catch (Exception $ex) {
            $transaction->rollBack();
            return FALSE;
        }
    }
    
    /*
     * get total comment
     * 
     * Auth : 
     * Create : 20-03-2017
     */
    
    public static function getTotalActivityByMember($type){
        $query = new \yii\db\Query();
        $query->select(['activity.activity_id'])
                ->from('activity');
        $query->where(['activity.member_id' => Yii::$app->user->identity->member_id]);
        $query->andWhere(['activity.type' => $type]);
        $query->andWhere(['activity.status' => Activity::STATUS_ACTIVE]);
        return $query->count();
    }
    
}