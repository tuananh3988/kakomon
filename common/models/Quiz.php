<?php

namespace common\models;

use Yii;
use yii\web\UploadedFile;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
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
            [['question_img'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, jpeg'],
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
            'answer_id' => 'Answer',
            'staff_create' => 'Staff Create',
            'delete_flag' => 'Delete Flag',
            'created_date' => 'Created Date',
            'updated_date' => 'Updated Date',
            'question_img' => 'Question Img'
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function safeAttributes()
    {
        $safe = parent::safeAttributes();
        return array_merge($safe, $this->extraFields());
    }
    
    /**
     * @inheritdoc
     */
    public function extraFields()
    {
        return ['question_img', 'answer'];
    }
    
    /*
     * Validate Answer
     * 
     * Auth :
     * Create : 15-02-2017
     */
    public function validateAnswer($dataPost, $answer){
        if ($this->answer_id) {
            if (empty($answer['answer'.$this->answer_id]->content) && UploadedFile::getInstance($answer['answer'.$this->answer_id], '[answer'.$this->answer_id.']answer_img') == NULL) {
                $this->addError('answer_id', 'Answer not map');
                return false;
            }
            return TRUE;
        } else {
            return TRUE;
        }
    }
    
    /*
     * add new question
     * 
     * Auth : 
     * Create : 15-02-2017
     */
    
    public function addQuiz($dataPost, $answer, $flag){
        $transaction = \yii::$app->getDb()->beginTransaction();
        try {
            $this->load($dataPost);
            $answer['answer1']->setAttributes($dataPost['Answer']['answer1']);
            $answer['answer2']->setAttributes($dataPost['Answer']['answer2']);
            $answer['answer3']->setAttributes($dataPost['Answer']['answer3']);
            $answer['answer4']->setAttributes($dataPost['Answer']['answer4']);
            $answer['answer5']->setAttributes($dataPost['Answer']['answer5']);
            $answer['answer6']->setAttributes($dataPost['Answer']['answer6']);
            $answer['answer7']->setAttributes($dataPost['Answer']['answer7']);
            $answer['answer8']->setAttributes($dataPost['Answer']['answer8']);
            
            
            if ($this->validate() && $this->validateAnswer($dataPost, $answer)) {
                //insert table quiz
                $this->staff_create = Yii::$app->user->identity->id;
                $this->save();
                //insert table answer
                foreach ($dataPost['Answer'] as $key => $value) {
                    if (!empty($answer[$key]->content) || UploadedFile::getInstance($answer[$key], '['.$key.']answer_img') != NULL) {
                        $order = (int) filter_var($key,FILTER_SANITIZE_NUMBER_INT);
                        $answer[$key]->quiz_id = $this->quiz_id;
                        $answer[$key]->order = $order;
                        $answer[$key]->save();
                    }
                    //
                }
                //
                $transaction->commit();
                var_dump($this->quiz_id);die;
            }
//            var_dump($validateAnswer);die;
//            var_dump(UploadedFile::getInstance($answer['answer1'], '[answer1]answer_img'));die('hien');
//            var_dump(UploadedFile::getInstance($answer['answer1'], 'answer_img'));die('2121');
//            $transaction->commit();
        } catch (Exception $ex) {
            $transaction->rollBack();
        }
        
        return TRUE;
    }
}
