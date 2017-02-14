<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "quiz".
 *
 * @property integer $quiz_id
 * @property integer $type
 * @property string $question
 * @property integer $category_id_1
 * @property integer $category_id_2
 * @property integer $category_id_3
 * @property integer $category_id_4
 * @property integer $answer_id
 * @property integer $staff_create
 * @property integer $delete_flag
 * @property string $created_date
 * @property string $updated_date
 */
class Quiz extends \yii\db\ActiveRecord
{
    public $question_img;

    const TYPE_DEFAULT = 1;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'quiz';
    }

    public static $TYPE = [
        1 => 'Normal',
        2 => 'Quick quiz',
        3 => 'Collect',
    ];
    
    
    public function __construct()
    {
        $this->type = self::TYPE_DEFAULT;
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type', 'category_id_1', 'category_id_2', 'category_id_3', 'category_id_4', 'answer_id', 'staff_create', 'delete_flag'], 'integer'],
            [['question'], 'required'],
            [['created_date', 'updated_date'], 'safe'],
            [['question'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'quiz_id' => 'Quiz ID',
            'type' => 'Type',
            'question' => 'Question',
            'category_id_1' => 'Category Id 1',
            'category_id_2' => 'Category Id 2',
            'category_id_3' => 'Category Id 3',
            'category_id_4' => 'Category Id 4',
            'answer_id' => 'Answer ID',
            'staff_create' => 'Staff Create',
            'delete_flag' => 'Delete Flag',
            'created_date' => 'Created Date',
            'updated_date' => 'Updated Date',
            'question_img' => 'Question Img'
        ];
    }
}
