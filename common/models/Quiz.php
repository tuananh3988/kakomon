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
    public $quiz_answer1;
    public $quiz_answer2;
    public $quiz_answer3;
    public $quiz_answer4;
    public $quiz_answer5;
    public $quiz_answer6;
    public $quiz_answer7;
    public $quiz_answer8;

    const TYPE_DEFAULT = 1;
    const TYPE_CREATE = 2;
    const QUIZ_ANSWER = '00000000';

    const TYPE_NORMAL = 1;
    const TYPE_QUICK_QUIZ = 2;
    const TYPE_COLLECT = 3;
    
    public static $QUIZ_CLASS = [
        '一般問題' => 1,
        '必修問題' => 2
    ];
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
            [['type', 'category_main_id', 'category_a_id', 'category_b_id', 'staff_create', 'delete_flag'], 'integer'],
            [['question'], 'required'],
            [['question'], 'string'],
            [['created_date', 'updated_date'], 'safe'],
            [['quiz_answer'], 'string', 'max' => 255],
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
            'category_main_id' => 'Category Id 1',
            'category_a_id' => 'Category Id 2',
            'category_b_id' => 'Category Id 3',
            'answer_id' => 'Answer',
            'quiz_answer' => 'Quiz Answer',
            'staff_create' => 'Staff Create',
            'delete_flag' => 'Delete Flag',
            'created_date' => 'Created Date',
            'updated_date' => 'Updated Date',
            'question_img' => 'Question Img',
            'remove_img_question_flg' => 'remove_img_question_flg',
            'quiz_answer1' => 'Quiz Answer1',
            'quiz_answer2' => 'Quiz Answer2',
            'quiz_answer3' => 'Quiz Answer3',
            'quiz_answer4' => 'Quiz Answer4',
            'quiz_answer5' => 'Quiz Answer5',
            'quiz_answer6' => 'Quiz Answer6',
            'quiz_answer7' => 'Quiz Answer7',
            'quiz_answer8' => 'Quiz Answer8',
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
        return ['question_img', 'answer', 'remove_img_question_flg', 'quiz_answer1', 'quiz_answer2', 'quiz_answer3', 'quiz_answer4',
            'quiz_answer5', 'quiz_answer6', 'quiz_answer7', 'quiz_answer8'];
    }
    
    /*
     * Validate Answer
     * 
     * Auth :
     * Create : 15-02-2017
     */
    public function validateAnswer($dataPost, $answer, $flag, $idQuiz = null)
    {
        $session = Yii::$app->session;
        foreach ($answer as $key => $value) {
            $id = (int) filter_var($key,FILTER_SANITIZE_NUMBER_INT);
            $utility = new Utility();
            $keyQuizAns = 'quiz_answer'.$id;
            if ($flag == 0) {
                if (($dataPost['Quiz'][$keyQuizAns] == 1) && empty($answer['answer'.$id]->content) && (UploadedFile::getInstance($answer['answer'.$id], '[answer'.$id.']answer_img') == NULL)) {
                    $session->setFlash('validate_answer','Answer not map');
                    return FALSE;
                    break;
                }
              
            } else {
                if (($dataPost['Quiz'][$keyQuizAns] == 1) &&  empty($answer['answer'.$id]->content) && (UploadedFile::getInstance($answer['answer'.$id], '[answer'.$id.']answer_img') == NULL) && (!$utility->checkExitImages('answer', $idQuiz, $id) || $answer['answer'.$id]->remove_img_flg == 1)) {
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
    
    public function addQuiz($dataPost, $answer, $flag, $type = self::TYPE_DEFAULT){
        $transaction = \yii::$app->getDb()->beginTransaction();
        try {
            $this->load($dataPost);
            for ($i = 1; $i <= 8; $i++) {
                $answer['answer'.$i]->setAttributes($dataPost['Answer']['answer'.$i]);
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
                $this->quiz_answer = Utility::renderQuizAnswer($dataPost);
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
        $query->andFilterWhere(['=', 'category_main_id' , $this->category_main_id]);
        $query->andFilterWhere(['=', 'category_a_id' , $this->category_a_id]);
        $query->andFilterWhere(['=', 'category_b_id' , $this->category_b_id]);
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
    
    /*
     * Render list subcategory
     * 
     * Auth : 
     * Create : 08-03-2017
     */
    
    public static function renderListSubCat($subCat2 , $subCat3, $subCat4){
        $list = [];
        if (!empty($subCat2)) {
            $sub2 = Category::findOne(['cateory_id' => $subCat2]);
            $list[] = $sub2->name;
        }
        if (!empty($subCat3)) {
            $sub3 = Category::findOne(['cateory_id' => $subCat3]);
            $list[] = $sub3->name;
        }
        if (!empty($subCat4)) {
            $sub4 = Category::findOne(['cateory_id' => $subCat4]);
            $list[] = $sub4->name;
        }
        return $list;
    }
}
