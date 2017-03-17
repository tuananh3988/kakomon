<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
/**
 * This is the model class for table "member_quiz_activity".
 *
 * @property integer $member_quiz_activity_id
 * @property integer $member_id
 * @property integer $quiz_id
 * @property integer $delete_flag
 * @property string $updated_date
 * @property string $created_date
 */
class MemberQuizActivity extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'member_quiz_activity';
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
            [['member_id', 'quiz_id'], 'required'],
            [['member_id', 'quiz_id', 'delete_flag'], 'integer'],
            [['updated_date', 'created_date'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'member_quiz_activity_id' => 'Member Quiz Activity ID',
            'member_id' => 'Member ID',
            'quiz_id' => 'Quiz ID',
            'delete_flag' => 'Delete Flag',
            'updated_date' => 'Updated Date',
            'created_date' => 'Created Date',
        ];
    }
    
    /*
     * Get list ans for member
     * 
     * Auth : 
     * Create : 18-03-2017
     */
    
    public function getListAnsForMember(){
        $query = new \yii\db\Query();
        $query->select(['member_quiz_activity.*', 'quiz.*'])
                ->from('member_quiz_activity');
        $query->join('INNER JOIN', 'quiz', 'quiz.quiz_id = member_quiz_activity.quiz_id');
        $query->where(['=', 'quiz.delete_flag', Quiz::QUIZ_ACTIVE]);
        $query->andWhere(['=', 'member_quiz_activity.member_id', Yii::$app->user->identity->member_id]);
        return $query->all();
    }
}
