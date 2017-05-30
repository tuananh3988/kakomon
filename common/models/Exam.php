<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\data\ActiveDataProvider;
use common\models\ExamQuiz;

/**
 * This is the model class for table "exam".
 *
 * @property integer $exam_id
 * @property string $name
 * @property integer $status
 * @property integer $type
 * @property integer $total_quiz
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

    public static $TYPEEXAM = [
        1 => 'Free',
        2 => 'Paid'
    ];
    
    public static $STATUS = [
        0 => 'Create',
        1 => 'Active',
        2 => 'End'
    ];
    
    const EXAM_STATUS_CREATED = 0;
    const EXAM_STATUS_ACTIVE = 1;
    const EXAM_STATUS_END = 2;
    
    const SCENARIO_EXAM_DETAIL = 'detail-exam';
    const SCENARIO_EXAM_DETAIL_ACTIVE = 'detail-exam-active';
    
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
    public function rules()
    {
        return [
            [['name', 'total_quiz', 'end_date'], 'required'],
            [['status', 'type', 'total_quiz'], 'integer'],
            [['start_date', 'end_date', 'created_date', 'updated_date'], 'safe'],
            [['name'], 'string', 'max' => 255],
            ['end_date', 'validateEndDate'],
            
            [['exam_id'], 'required', 'on' => self::SCENARIO_EXAM_DETAIL],
            [['exam_id'], 'integer', 'on' => self::SCENARIO_EXAM_DETAIL],
            ['exam_id', 'validateExamId', 'on' => self::SCENARIO_EXAM_DETAIL],
            
            [['exam_id'], 'required', 'on' => self::SCENARIO_EXAM_DETAIL_ACTIVE],
            [['exam_id'], 'integer', 'on' => self::SCENARIO_EXAM_DETAIL_ACTIVE],
            ['exam_id', 'validateExamIdActive', 'on' => self::SCENARIO_EXAM_DETAIL_ACTIVE],
            
            [['exam_id'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        
        $scenarios[self::SCENARIO_EXAM_DETAIL] = ['exam_id'];
        return $scenarios;
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'exam_id' => 'Exam ID',
            'name' => 'Name',
            'status' => 'Status',
            'type' => 'Type',
            'total_quiz' => 'Total Quiz',
            'start_date' => 'Start Date',
            'end_date' => 'End Date',
            'created_date' => 'Created Date',
            'updated_date' => 'Updated Date',
        ];
    }
    
    /*
     * validate end_date
     * 
     * Auth:
     * Create : 19-02-2017 
     */
    
    public function validateEndDate($attribute){
        if (strtotime($this->$attribute) < strtotime(date('Y-m-d H:i:s'))) {
            $this->addError($attribute, \Yii::t('app', 'End time must be greater than the current time', ['attribute' => $this->attributeLabels()[$attribute]]));
        }
    }
    
    /*
     * validate exam-id
     * 
     * Auth:
     * Create : 19-02-2017 
     */
    
    public function validateExamId($attribute){
        if (!$this->hasErrors()) {
            $examDetail = Exam::findOne(['exam_id' => $this->$attribute]);
            if (!$examDetail) {
                $this->addError($attribute, \Yii::t('app', 'data not exist', ['attribute' => $this->attributeLabels()[$attribute]]));
            }
        }
    }
    
    /*
     * validate exam-id active
     * 
     * Auth:
     * Create : 19-02-2017 
     */
    
    public function validateExamIdActive($attribute){
        if (!$this->hasErrors()) {
            $examDetail = Exam::findOne(['exam_id' => $this->$attribute, 'status' => self::EXAM_STATUS_ACTIVE]);
            if (!$examDetail) {
                $this->addError($attribute, \Yii::t('app', 'data not exist', ['attribute' => $this->attributeLabels()[$attribute]]));
            }
        }
    }
    
    /**
     * get list user
     * @Date 19-02-2017 
     */
    public function getData() {
        $query = new \yii\db\Query();
        $query->select(['exam.*'])
                ->from('exam');
        $query->andFilterWhere(['like', 'name' , $this->name]);
        $query->andFilterWhere(['>=', 'DATE_FORMAT(exam.start_date,"%Y/%m/%d")', $this->start_date]);
        $query->andFilterWhere(['<=', 'DATE_FORMAT(exam.end_date,"%Y/%m/%d")', $this->end_date]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => Yii::$app->params['pageSize']
            ],
            'sort' => [
                'defaultOrder' => [
                    'exam_id' => SORT_DESC,
                    'start_date' => SORT_DESC,
                    'end_date' => SORT_DESC
                ]
            ],
        ]);
        $dataProvider->sort->attributes['exam_id'] = [
            'desc' => ['exam.exam_id' => SORT_DESC],
            'asc' => ['exam.exam_id' => SORT_ASC],
        ];
        $dataProvider->sort->attributes['start_date'] = [
            'desc' => ['exam.start_date' => SORT_DESC],
            'asc' => ['exam.start_date' => SORT_ASC],
        ];
        $dataProvider->sort->attributes['end_date'] = [
            'desc' => ['exam.end_date' => SORT_DESC],
            'asc' => ['exam.end_date' => SORT_ASC],
        ];
        return $dataProvider;
    }
    
    /*
     * render list exam
     * 
     * Auth :
     * Create : 19-02-2017
     */
    
    public static function renderListExam($quizId){
        $listExam = [];
        $query = new \yii\db\Query();
        $query->select(['exam.*'])
                ->from('exam');
        $query->where(['status' => 0]);
        $exam = $query->all();
        if (count($exam) > 0){
            foreach ($exam as $key => $value) {
                $examQuiz = ExamQuiz::find()->where(['exam_id' => $value['exam_id'], 'quiz_id' => $quizId])->one();
                $totalQuiz = ExamQuiz::getCountQuizByIdExam($value['exam_id']);
                if (!$examQuiz && ($totalQuiz < $value['total_quiz'])) {
                    $listExam[$value['exam_id']] = $value['name'];
                }
            }
        }
        return $listExam;
    }
    
    /*
     * Get info notification
     * 
     * Auth : 
     * Created : 06-04-2017
     */
    public static function getInforNotification($examId){
        $query = new \yii\db\Query();
        $query->select(['exam.*'])
                ->from('exam');
        $query->where(['exam.exam_id' => $examId]);
        return $query->one();
    }
    
    /*
     * Get list quiz exam
     * 
     * Auth : 
     * Created : 20-04-2017
     */
    
    public function getListQuizIdByExam() {
        $query = new \yii\db\Query();
        $query->select(['exam_quiz.quiz_id'])
                ->from('exam_quiz');
        $query->where(['exam_quiz.exam_id' => $this->exam_id]);
        $query->orderBy(['exam_quiz_id' => SORT_ASC]);
        return $query->all();
    }
}
