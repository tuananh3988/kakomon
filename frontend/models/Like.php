<?php

namespace frontend\models;

use Yii;
use yii\base\Model;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use common\models\Activity;
use common\models\MemberQuizActivity;
use common\models\ActivitySumary;
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
    
    /*
     * Save data like
     * 
     * Auth : 
     * Create : 20-03-2017
     */
    public function saveLike($activityDetail, $activityDetailDisLike){
        $transaction = \yii::$app->getDb()->beginTransaction();
        try {
            $modelActivitySave = new Activity();
            $modelActivitySave->member_id = Yii::$app->user->identity->member_id;
            $modelActivitySave->status = $this->status;
            $modelActivitySave->type = Activity::TYPE_LIKE;
            $modelActivitySave->quiz_id = $activityDetail->quiz_id;
            $modelActivitySave->relate_id = $this->activity_id;
            $modelActivitySave->save();
            
            //update status for record like
            if ($activityDetailDisLike) {
                $activityDetailDisLike->status = Activity::STATUS_DELETE;
                $activityDetailDisLike->save();
            }
            
            //insert table member_quiz_activity
            $memberQuizActivity = MemberQuizActivity::findOne(['member_id' => Yii::$app->user->identity->member_id, 'quiz_id' => $activityDetail->quiz_id]);
            if (!$memberQuizActivity) {
                $modelMemberQuizActivity = new MemberQuizActivity();
                $modelMemberQuizActivity->member_id = Yii::$app->user->identity->member_id;
                $modelMemberQuizActivity->quiz_id = $activityDetail->quiz_id;
                $modelMemberQuizActivity->save();
            }
            //insert or update table activity_sumary
            $activitySumary = ActivitySumary::findOne(['activity_id' => $this->activity_id, 'type' => ActivitySumary::TYPE_LIKE]);
            if (!$activitySumary) {
                $modelActivitySumary = new ActivitySumary();
                $modelActivitySumary->activity_id = $this->activity_id;
                $modelActivitySumary->total = 1;
                $modelActivitySumary->type = ActivitySumary::TYPE_LIKE;
                $modelActivitySumary->save();
            } else {
                $activitySumary->total = $activitySumary->total + 1;
                $activitySumary->save();
            }
            
            $transaction->commit();
            return $modelActivitySave->activity_id;
        } catch (Exception $ex) {
            $transaction->rollBack();
            return FALSE;
        }
    }
    
    /*
     * update data like or dislike
     * 
     * Auth : 
     * Create : 20-03-2017
     */
    
    public function updateLikeOrDisLike($activityDetailOld, $activityDetail, $type){
         $transaction = \yii::$app->getDb()->beginTransaction();
        try {
            $activityDetailOld->status = $this->status;
            $activityDetailOld->save();
            //update table activity_sumary
            $activitySumary = ActivitySumary::findOne(['activity_id' => $this->activity_id, 'type' => $type]);
            if ($activitySumary) {
                $activitySumary->total = $activitySumary->total - 1;
                $activitySumary->save();
            }
            //update table member_quiz_activity
            if (count(Activity::checkActivityForMember($activityDetail->quiz_id)) == 0) {
                $memberQuizActivity = MemberQuizActivity::findOne(['member_id' => Yii::$app->user->identity->member_id, 'quiz_id' => $activityDetail->quiz_id]);
                if ($memberQuizActivity) {
                    $memberQuizActivity->delete_flag = MemberQuizActivity::DELETE_DELETE;
                    $memberQuizActivity->save();
                }
            }
            $transaction->commit();
            return TRUE;
        } catch (Exception $ex) {
            $transaction->rollBack();
            return FALSE;
        }
        
    }
    
    /*
     * Save data dislike
     * 
     * Auth : 
     * Create : 20-03-2017
     */
    public function saveDisLike($activityDetail, $activityDetailLike){
        $transaction = \yii::$app->getDb()->beginTransaction();
        try {
            $modelActivitySave = new Activity();
            $modelActivitySave->member_id = Yii::$app->user->identity->member_id;
            $modelActivitySave->status = $this->status;
            $modelActivitySave->type = Activity::TYPE_DISLIKE;
            $modelActivitySave->quiz_id = $activityDetail->quiz_id;
            $modelActivitySave->relate_id = $this->activity_id;
            $modelActivitySave->save();
            //update status for record like
            if ($activityDetailLike) {
                $activityDetailLike->status = Activity::STATUS_DELETE;
                $activityDetailLike->save();
            }
            
            //insert table member_quiz_activity
            $memberQuizActivity = MemberQuizActivity::findOne(['member_id' => Yii::$app->user->identity->member_id, 'quiz_id' => $activityDetail->quiz_id]);
            if (!$memberQuizActivity) {
                $modelMemberQuizActivity = new MemberQuizActivity();
                $modelMemberQuizActivity->member_id = Yii::$app->user->identity->member_id;
                $modelMemberQuizActivity->quiz_id = $activityDetail->quiz_id;
                $modelMemberQuizActivity->save();
            }
            //insert or update table activity_sumary
            $activitySumary = ActivitySumary::findOne(['activity_id' => $this->activity_id, 'type' => ActivitySumary::TYPE_DIS_LIKE]);
            if (!$activitySumary) {
                $modelActivitySumary = new ActivitySumary();
                $modelActivitySumary->activity_id = $this->activity_id;
                $modelActivitySumary->total = 1;
                $modelActivitySumary->type = ActivitySumary::TYPE_DIS_LIKE;
                $modelActivitySumary->save();
            } else {
                $activitySumary->total = $activitySumary->total + 1;
                $activitySumary->save();
            }
            
            $transaction->commit();
            return $modelActivitySave->activity_id;
        } catch (Exception $ex) {
            $transaction->rollBack();
            return FALSE;
        }
    }
}