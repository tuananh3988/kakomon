<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "exam".
 *
 * @property integer $exam_id
 * @property integer $status
 * @property integer $type
 * @property integer $ total_quiz
 * @property string $start_date
 * @property string $end_date
 * @property string $created_date
 * @property string $updated_date
 */
class Exam extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'exam';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['status', 'type', ' total_quiz'], 'integer'],
            [['start_date', 'end_date'], 'required'],
            [['start_date', 'end_date', 'created_date', 'updated_date'], 'safe'],
        ];
    }

    public static $TYPEEXAM = [
        1 => 'Free',
        2 => 'Paid'
    ];
    
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
    
    public function __construct()
    {
        $this->status = 0;
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'exam_id' => 'Exam ID',
            'status' => 'Status',
            'type' => 'Type',
            ' total_quiz' => 'Total Quiz',
            'start_date' => 'Start Date',
            'end_date' => 'End Date',
            'created_date' => 'Created Date',
            'updated_date' => 'Updated Date',
        ];
    }
}
