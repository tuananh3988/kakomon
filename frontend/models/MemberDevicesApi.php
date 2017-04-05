<?php

namespace frontend\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use common\models\MemberDevices;
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
class MemberDevicesApi extends \yii\db\ActiveRecord
{
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
            [['device_id'], 'required'],
            ['device_id', 'validateDeviceId'],
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
            'device_id' => 'Device ID'
        ];
    }
    /*
     * Validate device_id
     * 
     * Auth : 
     * Created : 05-04-2017
     */
    public function validateDeviceId($attribute)
    {
        if (!$this->hasErrors()) {
            $deviceId = MemberDevices::findOne(['member_id' => Yii::$app->user->identity->member_id, 'device_id' => $this->device_id]);
            if (!$deviceId) {
                $this->addError($attribute, \Yii::t('app', 'data not exist', ['attribute' => $this->attributeLabels()[$attribute]]));
            }
        }
    }
}
