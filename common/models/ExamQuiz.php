<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "exam_quiz".
 *
 * @property integer $exam_quiz_id
 * @property integer $exam_id
 * @property integer $quiz_id
 * @property string $created_date
 * @property string $updated_date
 */
class ExamQuiz extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'exam_quiz';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['exam_id', 'quiz_id'], 'required'],
            [['exam_id', 'quiz_id'], 'integer'],
            [['created_date', 'updated_date'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'exam_quiz_id' => 'Exam Quiz ID',
            'exam_id' => 'Exam ID',
            'quiz_id' => 'Quiz ID',
            'created_date' => 'Created Date',
            'updated_date' => 'Updated Date',
        ];
    }
}
