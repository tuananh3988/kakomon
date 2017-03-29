<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "activity".
 *
 * @property integer $comment_id
 * @property integer $member_id
 * @property integer $type
 * @property integer $quiz_id
 * @property integer $relate_id
 * @property string $content
 * @property string $created_date
 * @property string $updated_date
 */
class Activity extends \yii\db\ActiveRecord
{
    
    const TYPE_COMMENT = 1;
    const TYPE_HELP = 2;
    const TYPE_REPLY = 3;
    const TYPE_LIKE = 4;
    const TYPE_DISLIKE = 5;
    
    const STATUS_ACTIVE = 1;
    const STATUS_DELETE = 2;

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
            [['member_id', 'quiz_id'], 'required'],
            [['member_id', 'type', 'quiz_id', 'relate_id'], 'integer'],
            [['created_date', 'updated_date'], 'safe'],
            [['content'], 'string', 'max' => 255],
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
            'comment_id' => 'Comment ID',
            'member_id' => 'Member ID',
            'type' => 'Type',
            'quiz_id' => 'Quiz ID',
            'relate_id' => 'Relate ID',
            'content' => 'Content',
            'created_date' => 'Created Date',
            'updated_date' => 'Updated Date',
        ];
    }
    
    /*
     * Get total like
     * 
     * Auth : 
     * Creat : 28-02-2017
     */
    
    public static function getTotalLikeByMember($memberId)
    {
        return Activity::find()->where(['member_id' => $memberId, 'type' => self::TYPE_LIKE])->count();
    }
    
     /*
     * Get total dis like
     * 
     * Auth : 
     * Creat : 6-03-2017
     */
    
    public static function getTotalDisLikeByMember($memberId)
    {
        return Activity::find()->where(['member_id' => $memberId, 'type' => self::TYPE_DISLIKE])->count();
    }
    
    /*
     * Get total comment
     * 
     * Auth : 
     * Creat : 28-02-2017
     */
    
    public static function getTotalCommentByMember($memberId)
    {
        return Activity::find()->where(['member_id' => $memberId, 'type' => self::TYPE_COMMENT])->count();
    }
    
    /*
     * Get List commnet
     * 
     * Auth : 
     * Create : 02-03-2017
     */
    
    public function getListComment($memberId, $limit, $offset)
    {
        $query = new \yii\db\Query();
        $query->select(['activity.*'])
                ->from('activity');
        $query->where(['=', 'activity.member_id', $memberId]);
        $query->andWhere(['=', 'activity.type', self::TYPE_COMMENT]);
        $query->andWhere(['=', 'activity.status', self::STATUS_ACTIVE]);
        $query->offset($offset);
        $query->limit($limit);
        return $query->all();
    }
    
    /*
     * 
     * Auth : 
     * Created : 20-03-2017
     */
    
    public static function checkActivityForMember($quizId){
        $query = new \yii\db\Query();
        $query->select(['activity.*'])
                ->from('activity');
        $query->where(['=', 'activity.member_id', Yii::$app->user->identity->member_id]);
        $query->andWhere(['=', 'activity.quiz_id', $quizId]);
        $query->andWhere(['=', 'activity.status', self::STATUS_ACTIVE]);
        $query->andWhere([
            'or',
            'activity.type = '.self::TYPE_COMMENT,
            'activity.type = '. self::TYPE_LIKE,
            'activity.type = '. self::TYPE_DISLIKE,
            
        ]);
        return $query->all();
    }
    
    /*
     * get total Activity by category
     * 
     * Auth : 
     * Created : 22-03-2017
     */
    public static function getTotalQuizActivityByCategory($catId, $type){
        $query = new \yii\db\Query();
        $query->select(['quiz.quiz_id'])
                ->from('quiz');
        $query->join('INNER JOIN', 'activity', 'quiz.quiz_id = activity.quiz_id');
        $query->where(['quiz.type' => Quiz::TYPE_DEFAULT]);
        $query->andWhere(['quiz.category_main_id' => $catId]);
        $query->andWhere(['quiz.delete_flag' => Quiz::QUIZ_ACTIVE]);
        $query->andWhere(['activity.member_id' => Yii::$app->user->identity->member_id]);
        $query->andWhere(['activity.type' => $type]);
        $query->andWhere(['activity.status' => self::STATUS_ACTIVE]);
        return $query->count();
    }
    
    /*
     * get info activity
     * 
     * Auth : 
     * Created : 22-03-2017
     */
    public static function getInforActivity($activityId) {
        $query = new \yii\db\Query();
        $query->select(['activity_member.*', 'member.name'])
                ->from('activity');
        $query->join('INNER JOIN', 'activity as activity_member', 'activity_member.activity_id = activity.relate_id');
        $query->join('INNER JOIN', 'member', 'member.member_id = activity_member.member_id');
        $query->where(['activity.activity_id' => $activityId]);
        $query->andWhere(['quiz.delete_flag' => Quiz::QUIZ_ACTIVE]);
        $query->andWhere(['activity.member_id' => Yii::$app->user->identity->member_id]);
        $query->andWhere(['activity.type' => $type]);
        $query->andWhere(['activity.status' => self::STATUS_ACTIVE]);
        return $query->count();
    }
}
