<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\data\ActiveDataProvider;
/**
 * This is the model class for table "member_quiz_search_history".
 *
 * @property integer $member_quiz_search_history_id
 * @property integer $member_id
 * @property integer $quiz_class
 * @property integer $category_main_id
 * @property integer $category_a_id
 * @property integer $category_b_id
 * @property integer $quiz_year
 * @property integer $type
 * @property string $created_date
 */
class MemberQuizSearchHistory extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'member_quiz_search_history';
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
            [['member_id'], 'required'],
            [['member_id', 'quiz_class', 'type'], 'integer'],
            [['created_date'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'member_quiz_search_history_id' => 'Member Quiz Search History ID',
            'member_id' => 'Member ID',
            'quiz_class' => 'Quiz Class',
            'category_main_id' => 'Category Main ID',
            'category_a_id' => 'Category A ID',
            'category_b_id' => 'Category B ID',
            'quiz_year' => 'Quiz Year',
            'type' => 'Type',
            'created_date' => 'Created Date',
        ];
    }
    
    /*
     * Get list History search
     * 
     * Auth : 
     * Created : 16-03-2017
     */
    
    public function getListHistorySearch($limit, $offset, $flag = false)
    {
        $query = new \yii\db\Query();
        $query->select(['member_quiz_search_history.*'])
                ->from('member_quiz_search_history');
        $query->where(['=', 'member_quiz_search_history.member_id', Yii::$app->user->identity->member_id]);
        $query->orderBy(['member_quiz_search_history.member_quiz_search_history_id' => SORT_DESC]);
        $query->offset($offset);
        $query->limit($limit);
        if ($flag) {
            return $query->count();
        }
        return $query->all();
    }
}
