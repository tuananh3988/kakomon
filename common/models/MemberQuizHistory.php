<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "member_quiz_history".
 *
 * @property integer $member_quiz_history_id
 * @property integer $quiz_id
 * @property integer $exam_id
 * @property integer $member_id
 * @property string $answer
 * @property integer $correct_flag
 * @property integer $time
 * @property string $created_date
 */
class MemberQuizHistory extends \yii\db\ActiveRecord
{
    
    const FLAG_CORRECT_CORRECT = 1;
    const FLAG_CORRECT_INCORRECT = 2;
    const FLAG_CORRECT_NOT_DOING = 3;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'member_quiz_history';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['quiz_id', 'member_id', 'answer'], 'required'],
            [['quiz_id', 'exam_id', 'member_id', 'correct_flag', 'time'], 'integer'],
            [['created_date'], 'safe'],
            [['answer'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'member_quiz_history_id' => 'Member Quiz History ID',
            'quiz_id' => 'Quiz ID',
            'exam_id' => 'Exam ID',
            'member_id' => 'Member ID',
            'answer' => 'Answer',
            'correct_flag' => 'Correct Flag',
            'time' => 'Time',
            'created_date' => 'Created Date',
        ];
    }
}
