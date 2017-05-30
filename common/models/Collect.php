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
class Collect extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'collect';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['collect_name'], 'required'],
            [['created_date', 'updated_date'], 'safe'],
            [['collect_name'], 'string', 'max' => 255],
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
            'collect_id' => 'Collect ID',
            'collect_name' => 'Collect Name',
            'created_date' => 'Created Date',
            'updated_date' => 'Updated Date',
        ];
    }
    
    /*
     * Get info notification
     * 
     * Auth : 
     * Created : 06-04-2017
     */
    public static function getInforNotification($collectId){
        $query = new \yii\db\Query();
        $query->select(['collect.*'])
                ->from('collect');
        $query->where(['collect.collect_id' => $collectId]);
        return $query->one();
    }
}
