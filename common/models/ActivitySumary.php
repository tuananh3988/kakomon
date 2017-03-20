<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
/**
 * This is the model class for table "activity_sumary".
 *
 * @property integer $activity_sumary_id
 * @property integer $activity_id
 * @property integer $total
 * @property integer $type
 * @property string $created_date
 * @property string $updated_date
 */
class ActivitySumary extends \yii\db\ActiveRecord
{
    
    const TYPE_LIKE = 1;
    const TYPE_DIS_LIKE = 2;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'activity_sumary';
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
            [['activity_sumary_id', 'activity_id'], 'required'],
            [['activity_sumary_id', 'activity_id', 'total', 'type'], 'integer'],
            [['created_date', 'updated_date'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'activity_sumary_id' => 'Activity Sumary ID',
            'activity_id' => 'Activity ID',
            'total' => 'Total',
            'type' => 'Type',
            'created_date' => 'Created Date',
            'updated_date' => 'Updated Date',
        ];
    }
}
