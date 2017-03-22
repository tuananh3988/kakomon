<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
/**
 * This is the model class for table "member_category_time".
 *
 * @property integer $member_category_time_id
 * @property integer $member_id
 * @property integer $category_id
 * @property integer $total_time
 * @property string $created_date
 * @property string $updated_date
 */
class MemberCategoryTime extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'member_category_time';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['member_id', 'category_id'], 'required'],
            [['member_id', 'category_id', 'total_time'], 'integer'],
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
            'member_category_time_id' => 'Member Category Time ID',
            'member_id' => 'Member ID',
            'category_id' => 'Category ID',
            'total_time' => 'Total Time',
            'created_date' => 'Created Date',
            'updated_date' => 'Updated Date',
        ];
    }
    
    /*
     * get Total time view category
     * 
     * Auth : 
     * Created : 22-03-2017
     */
    
    public static function getTotalTimeViewByMainCategory($catId){
        $total = MemberCategoryTime::find()->where(['member_id' => Yii::$app->user->identity->member_id, 'category_id' => $catId])->one();
        if (!$total) {
            return null;
        }
        return $total->total_time;
    }
}
