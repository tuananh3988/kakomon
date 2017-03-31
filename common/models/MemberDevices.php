<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
/**
 * This is the model class for table "member_devices".
 *
 * @property integer $id
 * @property string $member_id
 * @property string $device_id
 * @property integer $device_type
 * @property string $device_token
 * @property integer $delete_flag
 * @property string $created_date
 * @property string $updated_date
 */
class MemberDevices extends \yii\db\ActiveRecord
{
    const DEVICE_TYPE_IOS = 1;
    const DEVICE_TYPE_AOS = 2;
    
    const DEVICE_ACTIVE = 0;
    const DEVICE_DELETED = 1;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'member_devices';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['member_id'], 'required'],
            [['id', 'member_id', 'device_type', 'delete_flag'], 'integer'],
            [['created_date', 'updated_date'], 'safe'],
            [['device_id'], 'string', 'max' => 64],
            [['device_token'], 'string', 'max' => 256],
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
            'id' => 'ID',
            'member_id' => 'Member ID',
            'device_id' => 'Device ID',
            'device_type' => 'Device Type',
            'device_token' => 'Device Token',
            'delete_flag' => 'Delete Flag',
            'created_date' => 'Created Date',
            'updated_date' => 'Updated Date',
        ];
    }
}
