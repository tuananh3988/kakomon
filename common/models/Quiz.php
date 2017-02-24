<?php

namespace common\models;

use Yii;
use yii\web\UploadedFile;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use common\components\Utility;
use common\models\Answer;
use yii\data\ActiveDataProvider;
use yii\web\Session;
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
    public $remove_img_question_flg;

    const TYPE_DEFAULT = 1;
    const TYPE_CREATE = 2;

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
        $this->remove_img_question_flg = 0;
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type', 'category_id_1', 'category_id_2', 'category_id_3', 'category_id_4', 'staff_create', 'delete_flag'], 'integer'],
            [['question'], 'required'],
            [['created_date', 'updated_date'], 'safe'],
            //[['question'], 'string', 'max' => 255],
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
            'question_img' => 'Question Img',
            'remove_img_question_flg' => 'remove_img_question_flg'
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
        return ['question_img', 'answer', 'remove_img_question_flg'];
    }
    
    /*
     * Validate Answer
     * 
     * Auth :
     * Create : 15-02-2017
     */
    public function validateAnswer($dataPost, $answer, $flag, $idQuiz = null){
        $session = Yii::$app->session;
        foreach ($answer as $key => $value) {
            $id = (int) filter_var($key,FILTER_SANITIZE_NUMBER_INT);
            $utility = new Utility();
            $keyQuizAns = 'quiz_answer'.$id;
            if ($flag == 0) {
                if (($dataPost['QuizAnswer'][$keyQuizAns]['quiz_ans_flg'] == 1) && empty($answer['answer'.$id]->content) && (UploadedFile::getInstance($answer['answer'.$id], '[answer'.$id.']answer_img') == NULL)) {
                    $session->setFlash('validate_answer','Answer not map');
                    return FALSE;
                    break;
                }
              
            } else {
                if (($dataPost['QuizAnswer'][$keyQuizAns]['quiz_ans_flg'] == 1) &&  empty($answer['answer'.$id]->content) && (UploadedFile::getInstance($answer['answer'.$id], '[answer'.$id.']answer_img') == NULL) && (!$utility->checkExitImages('answer', $idQuiz, $id) || $answer['answer'.$id]->remove_img_flg == 1)) {
                    $session->setFlash('validate_answer','Answer not map');
                    return FALSE;
                    break;
                }
            }
        }
        return true;
    }
    
    /*
     * add new question
     * 
     * Auth : 
     * Create : 15-02-2017
     */
    
    public function addQuiz($dataPost, $answer, $quizAnswer, $flag, $type = self::TYPE_DEFAULT){
        $transaction = \yii::$app->getDb()->beginTransaction();
        try {
            $this->load($dataPost);
            for ($i = 1; $i <= 8; $i++) {
                $answer['answer'.$i]->setAttributes($dataPost['Answer']['answer'.$i]);
                $quizAnswer['quiz_answer'.$i]->setAttributes($dataPost['QuizAnswer']['quiz_answer'.$i]);
                $answer['answer'.$i]->answer_img = UploadedFile::getInstance($answer['answer'.$i], '[answer'. $i .']answer_img');
            }
            $idQuiz = '';
            if ($flag == 1){
                $idQuiz = $this->quiz_id;
            }
            if ($this->validate() && $this->validateAnswer($dataPost, $answer, $flag, $idQuiz)) {
                $utility = new Utility();
                //insert table quiz
                $this->type = $type;
                $this->staff_create = Yii::$app->user->identity->id;
                $this->save();
                
                //upload images question
                if (UploadedFile::getInstance($this, 'question_img') != NULL) {
                    $utility->uploadImages(UploadedFile::getInstance($this, 'question_img'), 'question', $this->quiz_id);
                }
                
                //update image question
                if ($flag == 1) {
                    if ($this->remove_img_question_flg == 1) {
                        $utility->removeImages('question', $this->quiz_id);
                    }
                    if (UploadedFile::getInstance($this, 'question_img') != NULL) {
                        $utility->uploadImages(UploadedFile::getInstance($this, 'question_img'), 'question', $this->quiz_id);
                    }
                }
                
                //insert table answer
                foreach ($dataPost['Answer'] as $key => $value) {
                    $order = (int) filter_var($key,FILTER_SANITIZE_NUMBER_INT);
                    $keyQuizAns = 'quiz_answer'.$order;
                    if (!empty($answer[$key]->content) || UploadedFile::getInstance($answer[$key], '['.$key.']answer_img') != NULL) {
                        $answer[$key]->quiz_id = $this->quiz_id;
                        $answer[$key]->order = $order;
                        $answer[$key]->save();
                        if ($dataPost['QuizAnswer'][$keyQuizAns]['quiz_ans_flg'] == 1) {
                            $quizAnswer[$keyQuizAns]->quiz_id = $this->quiz_id;
                            $quizAnswer[$keyQuizAns]->answer_id = $answer[$key]->answer_id;
                            $quizAnswer[$keyQuizAns]->save();
                        }
                    }
                    //upload images ans
                    if (UploadedFile::getInstance($answer[$key], '['.$key.']answer_img') != NULL) {
                        $utility->uploadImages(UploadedFile::getInstance($answer[$key], '['.$key.']answer_img'), 'answer', $this->quiz_id,  $order);
                    }
                    //update images ans
                    if ($flag == 1) {
                        //update content ans if content null
                        if ($answer[$key]->order != NULL && empty($answer[$key]->content)) {
                            $answer[$key]->save();
                        }
                        //insert quiz answer
                        if ($utility->checkExitImages('answer', $this->quiz_id, $order) && ($dataPost['QuizAnswer'][$keyQuizAns]['quiz_ans_flg'] == 1)){
                            $quizAnswer[$keyQuizAns]->quiz_id = $this->quiz_id;
                            $quizAnswer[$keyQuizAns]->answer_id = $answer[$key]->answer_id;
                            $quizAnswer[$keyQuizAns]->save();
                        }
                        //remove images
                        if ($answer[$key]->remove_img_flg == 1) {
                            $utility->removeImages('answer', $this->quiz_id, $order);
                        }
                        //upload images
                        if (UploadedFile::getInstance($answer[$key], '['.$key.']answer_img') != NULL) {
                            $utility->uploadImages(UploadedFile::getInstance($answer[$key], '['.$key.']answer_img'), 'answer', $this->quiz_id,  $order);
                        }
                        //delete ans if not input content and input images
                        if (empty($answer[$key]->content) && UploadedFile::getInstance($answer[$key], '['.$key.']answer_img') == NULL && !$utility->checkExitImages('answer', $this->quiz_id, $order)) {
                            $answer[$key]->delete();
                            QuizAnswer::deleteAll(['quiz_id' => $this->quiz_id, 'answer_id' => $answer[$key]->answer_id]);
                        }
                        
                        //delete quiz_answer if not choise
                        if ($quizAnswer[$keyQuizAns]->quiz_answer_id && ($dataPost['QuizAnswer'][$keyQuizAns]['quiz_ans_flg'] == 0)) {
                            $quizAnswer[$keyQuizAns]->delete();
                        }
                    }
                }
                $transaction->commit();
                $message ='';
                if ($flag == 0) {
                    $message = 'Your create successfully question!';
                } elseif($flag == 1){
                    $message = 'You update successfully question!';
                }
                Yii::$app->session->setFlash('sucess_question',$message);
                if ($type == 1) {
                    return Yii::$app->response->redirect(['/question/index']);
                } else {
                    return Yii::$app->response->redirect(['/quick/index']);
                }
            }
        } catch (Exception $ex) {
            $transaction->rollBack();
            return Yii::$app->response->redirect(['/site/error']);
        }
        //return TRUE;
    }
    
    /**
     * get list user
     * @Date 15-02-2017
     */
    public function getData($type) {
        $query = new \yii\db\Query();
        $query->select(['quiz.*'])
                ->from('quiz');
        $query->andFilterWhere(['=', 'type' , $type]);
        $query->andFilterWhere(['=', 'delete_flag' , 0]);
        $query->andFilterWhere(['=', 'category_id_1' , $this->category_id_1]);
        $query->andFilterWhere(['=', 'category_id_2' , $this->category_id_2]);
        $query->andFilterWhere(['=', 'category_id_3' , $this->category_id_3]);
        $query->andFilterWhere(['=', 'category_id_4' , $this->category_id_4]);
        $query->andFilterWhere(['like', 'question' , $this->question]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => Yii::$app->params['pageSize']
            ],
            'sort' => [
                'defaultOrder' => [
                    'quiz_id' => SORT_DESC,
                    'category_id_1' => SORT_DESC,
                    'category_id_2' => SORT_DESC,
                    'category_id_3' => SORT_DESC,
                    'category_id_4' => SORT_DESC,
                    'created_date' => SORT_DESC
                ]
            ],
        ]);
        $dataProvider->sort->attributes['quiz_id'] = [
            'desc' => ['quiz.quiz_id' => SORT_DESC],
            'asc' => ['quiz.quiz_id' => SORT_ASC],
        ];
        $dataProvider->sort->attributes['category_id_1'] = [
            'desc' => ['quiz.category_id_1' => SORT_DESC],
            'asc' => ['quiz.category_id_1' => SORT_ASC],
        ];
        $dataProvider->sort->attributes['category_id_2'] = [
            'desc' => ['quiz.category_id_2' => SORT_DESC],
            'asc' => ['quiz.category_id_2' => SORT_ASC],
        ];
        $dataProvider->sort->attributes['category_id_3'] = [
            'desc' => ['quiz.category_id_3' => SORT_DESC],
            'asc' => ['quiz.category_id_3' => SORT_ASC],
        ];
        $dataProvider->sort->attributes['category_id_4'] = [
            'desc' => ['quiz.category_id_4' => SORT_DESC],
            'asc' => ['quiz.category_id_4' => SORT_ASC],
        ];
        $dataProvider->sort->attributes['created_date'] = [
            'desc' => ['quiz.created_date' => SORT_DESC],
            'asc' => ['quiz.created_date' => SORT_ASC],
        ];
        return $dataProvider;
    }
    
    /*
     * get list category by Id
     * 
     * Auth : 
     * Create :
     */
    
    public static function getListCategoryById($catId){
        $query = new \yii\db\Query();
        $query->select(['quiz.*'])
                ->from('quiz');
        $query->where(['delete_flag' => 0]);
        $query->andWhere([
            'or',
            'category_id_1 = ' . $catId,
            'category_id_2 = ' . $catId,
            'category_id_3 = ' . $catId,
            'category_id_4 = ' . $catId
        ]);
        return $query->all();
    }
}
