<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
/**
 * This is the model class for table "comment".
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
class Comment extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'comment';
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
    public function rules()
    {
        return [
            [['member_id', 'quiz_id', 'content'], 'required'],
            [['member_id', 'type', 'quiz_id', 'relate_id'], 'integer'],
            [['created_date', 'updated_date'], 'safe'],
            [['content'], 'string', 'max' => 255],
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
     * Get total comment
     * 
     * Auth : 
     * Create : 27-02-2017
     */
    
    public static function getTotalComment($memberId)
    {
        return Comment::find()->where(['member_id' => $memberId])->count();
    }
}
