<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "answer".
 *
 * @property integer $answer_id
 * @property integer $quiz_id
 * @property string $content
 * @property string $created_date
 * @property string $updated_date
 */
class Answer extends \yii\db\ActiveRecord {

    public $answer_img;
    public $remove_img_flg;

    const MAX_ANS = 8;
    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'answer';
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
    
    public function __construct()
    {
        $this->remove_img_flg = 0;
    }
    
    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            //[['quiz_id'], 'required'],
            [['quiz_id', 'order'], 'integer'],
            [['content'], 'string'],
            [['created_date', 'updated_date'], 'safe'],
            [['answer_img'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, jpeg'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'answer_id' => 'Answer ID',
            'quiz_id' => 'Quiz ID',
            'content' => 'Content',
            'created_date' => 'Created Date',
            'updated_date' => 'Updated Date',
            'answer_img' => 'Answer Img',
            'remove_img_flg' => 'Remove Img Flg'
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
        return ['answer_img', 'remove_img_flg'];
    }

}
