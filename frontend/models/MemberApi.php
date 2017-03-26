<?php

namespace frontend\models;

use Yii;
use yii\base\Model;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use common\models\Member;
/**
 * ContactForm is the model behind the contact form.
 */
class MemberApi extends \yii\db\ActiveRecord
{
    
    const SCENARIO_INFO = 'info';
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'member';
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['member_id'], 'required' , 'on' => self::SCENARIO_INFO],
            [['member_id'], 'validateMemberId', 'on' => self::SCENARIO_INFO],
            [['member_id', 'created_date', 'updated_date'], 'safe'],
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
            'member_id' => 'Member ID'
        ];
    }
    
    /*
     * validate member id
     * 
     * Auth : 
     * Create : 26-03-2017
     */
    
    public function validateMemberId($attribute){
        if (!$this->hasErrors()) {
            $memberDetail = Member::findOne(['member_id' => $this->$attribute]);
            if (!$memberDetail) {
                $this->addError($attribute, 'Member already exists');
            }
        }
    }
}