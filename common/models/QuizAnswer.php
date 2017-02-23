<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
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
    public $quiz_ans_flg;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'quiz_answer';
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
            'quiz_ans_flg' => 'Quiz Ans Flg'
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function safeAttributes() {
        $safe = parent::safeAttributes();
        return array_merge($safe, $this->extraFields());
    }

    /**
     * @inheritdoc
     */
    public function extraFields() {
        return ['quiz_ans_flg'];
    }
}
