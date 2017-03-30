<?php

namespace frontend\models;

use Yii;
use yii\base\Model;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use common\models\Activity;
use common\models\Quiz;
use common\components\Utility;
use frontend\models\Reply;
use common\models\ActivitySumary;
/**
 * ContactForm is the model behind the contact form.
 */
class Help extends \yii\db\ActiveRecord
{
    
    const SCENARIO_ADD_HELP = 'add';
    const SCENARIO_DELETE_HELP = 'delete';
    const SCENARIO_LIST_HELP = 'list';
    
    
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
            [['quiz_id', 'content'], 'required', 'on' => self::SCENARIO_ADD_HELP],
            [['quiz_id'], 'integer', 'on' => self::SCENARIO_ADD_HELP],
            ['quiz_id', 'validateQuizId', 'on' => self::SCENARIO_ADD_HELP],
            [['activity_id'], 'required', 'on' => self::SCENARIO_DELETE_HELP],
            ['activity_id', 'validateActivityId', 'on' => self::SCENARIO_DELETE_HELP],
            
            [['quiz_id'], 'required', 'on' => self::SCENARIO_LIST_HELP],
            ['quiz_id', 'validateQuizId', 'on' => self::SCENARIO_LIST_HELP],
            
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
    
    /*
     * Validate activity id
     * 
     * Auth :
     * Create : 01-03-2017
     * 
     */
    
    public function validateActivityId($attribute)
    {
        if (!$this->hasErrors()) {
            $activity = Activity::findOne(['activity_id' => $this->$attribute, 'member_id' => Yii::$app->user->identity->member_id, 'type' => Activity::TYPE_HELP]);
            if (!$activity) {
                $this->addError($attribute, \Yii::t('app', 'data not exist', ['attribute' => $this->attributeLabels()[$attribute]]));
            }
        }
    }
    
    /*
     * Get total help by quiz id
     * 
     * Auth : 
     * Create : 04-03-2017
     */
    public static function getTotalHelpByQuizId($quizId)
    {
        $query = new \yii\db\Query();
        $query->select('activity.activity_id')
                ->from('activity');
        $query->andWhere(['activity.quiz_id' => $quizId]);
        $query->andWhere(['activity.type' => Activity::TYPE_HELP]);
        $query->andWhere(['activity.status' => Activity::STATUS_ACTIVE]);
        return $query->count();
    }
    
    /*
     * Get List help
     * 
     * Auth : 
     * Creat : 02-03-2017
     */
    
    public static function getListHelpByQuizId($quizId, $limit, $offset)
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
        $query->andWhere(['activity.type' => Activity::TYPE_HELP]);
        $query->andWhere(['activity.status' => Activity::STATUS_ACTIVE]);
        $query->offset($offset);
        $query->limit($limit);
        $query->orderBy(['activity_id' => SORT_DESC]);
        return $query->all();
    }
    
    
    /*
     * Render List Help
     * 
     * Auth : 
     * Create : 02-03-2017
     */
    
    public static function renderListHelp($quizId, $limit, $offset)
    {
        $listData = [];
        $list = self::getListHelpByQuizId($quizId, $limit, $offset);
        if (count($list) > 0){
            foreach ($list as $key => $value) {
                $limit = Yii::$app->params['limit']['reply'];
                $offset = Yii::$app->params['offset']['reply'];
                $total = Reply::getTotalReplyByActivityId($value['activity_id']);
                $offsetReturn = Utility::renderOffset($total, $limit, $offset);
                
                $listData[] = [
                    'activity_id' => (int)$value['activity_id'],
                    'member_id' => (int)$value['meberId'],
                    'member_name' => $value['name'],
                    'avatar' => Utility::getImage('member', $value['meberId'], null, true),
                    'content' => $value['content'],
                    'isDisLike' => ($value['isDisLike']) ? true : false,
                    'isLike' => ($value['isLike']) ? true : false,
                    'total_like' => (int)$value['total_like'],
                    'total_dislike' => (int)$value['total_dislike'],
                    'reply' => Reply::renderListReply($value['activity_id'], $limit, $offset),
                    'total_reply' => (int)$total,
                    'offset_reply' => (int)$offsetReturn
                ];
            }
        }
        return $listData;
    }
    
    /*
     * update data delete help
     * 
     * Auth : 
     * Create : 20-03-2017
     */
    
    public function updateHelp($helpDetail, $activityId, $quizId){
        $transaction = \yii::$app->getDb()->beginTransaction();
        try {
            $helpDetail->status = Activity::STATUS_DELETE;
            $helpDetail->save();
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