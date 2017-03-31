<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
/**
 * This is the model class for table "notification".
 *
 * @property integer $notification_id
 * @property integer $type
 * @property string $title
 * @property integer $activity_id
 * @property integer $member_id
 * @property string $created_date
 * @property string $updated_date
 */
class Notification extends \yii\db\ActiveRecord
{
    
    const TYPE_LIKE = 1;
    const TYPE_REPLY = 2;
    const TYPE_FOLLOW = 3;
    const TYPE_QUICK_QUIZ = 4;
    const TYPE_EXAM = 5;
    const TYPE_COLLECT_QUIZ = 5;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'notification';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type', 'related_id', 'member_id'], 'integer'],
            [['created_date', 'updated_date'], 'safe'],
            [['title'], 'string', 'max' => 255],
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
            'notification_id' => 'Notification ID',
            'type' => 'Type',
            'title' => 'Title',
            'activity_id' => 'Activity ID',
            'member_id' => 'Member ID',
            'created_date' => 'Created Date',
            'updated_date' => 'Updated Date',
        ];
    }
}
