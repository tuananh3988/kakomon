<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "quiz_answer".
 *
 * @property integer $quiz_answer_id
 * @property integer $quiz_id
 * @property integer $answer_id
 * @property string $created_date
 * @property string $updated_date
 */
class QuizAnswer extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'quiz_answer';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['quiz_id', 'answer_id'], 'required'],
            [['quiz_id', 'answer_id'], 'integer'],
            [['created_date', 'updated_date'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'quiz_answer_id' => 'Quiz Answer ID',
            'quiz_id' => 'Quiz ID',
            'answer_id' => 'Answer ID',
            'created_date' => 'Created Date',
            'updated_date' => 'Updated Date',
        ];
    }
}
