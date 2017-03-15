<?php

namespace common\models;

use Yii;

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
}
