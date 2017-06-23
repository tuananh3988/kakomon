<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
/**
 * This is the model class for table "member_devices".
 *
 * @property integer $id
 * @property string $member_id
 * @property string $device_id
 * @property integer $device_type
 * @property string $device_token
 * @property integer $delete_flag
 * @property string $created_date
 * @property string $updated_date
 */
class ExamHistory extends \yii\db\ActiveRecord
{
    
    const SCENARIO_EXAM_HISTORY_DETAIL = 'detail-exam-history';
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'exam_history';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['exam_id', 'member_id', 'total_correct', 'total_not_doing'], 'integer'],
            [['created_date'], 'safe'],
            
            [['exam_id', 'contest_times'], 'required', 'on' => self::SCENARIO_EXAM_HISTORY_DETAIL],
            ['exam_id', 'validateExamId', 'on' => self::SCENARIO_EXAM_HISTORY_DETAIL],
        ];
    }
    
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                          ActiveRecord::EVENT_BEFORE_INSERT => ['created_date']
                ],
                'value' => date('Y-m-d H:i:s'),
            ],
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        
        $scenarios[self::SCENARIO_EXAM_HISTORY_DETAIL] = ['exam_id', 'contest_times'];
        return $scenarios;
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'exam_history_id' => 'Exam History Id',
            'exam_id' => 'Exam ID',
            'member_id' => 'Member ID',
            'total_correct' => 'Total Correct',
            'total_not_doing' => 'Total Not Doing',
            'contest_times' => 'Contest Times',
            'created_date' => 'Delete Flag'
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
    public function extraFields() {
        return ['contest_times'];
    }
    
    /*
     * validate exam id for finish
     * 
     * Auth:
     * Create : 21-06-2017 
     */
    public function validateExamId($attribute) {
        $examFinish = ExamHistory::find()->select('exam_history_id')->where(['exam_id' => $this->exam_id, 'contest_times' => $this->contest_times, 'member_id' => Yii::$app->user->identity->member_id])->one();
        if (!$examFinish) {
            $this->addError($attribute, \Yii::t('app', 'Data not exists', ['attribute' => $this->attributeLabels()[$attribute]]));
        }
    }
    
    /*
     * List exam
     * 
     * Auth : 
     * Created : 20-06-2017
     */
    public function getListExamByMember() {
        $query = new \yii\db\Query();
        $query->select(['exam_history.*'])
                ->from('exam_history');
        $query->where(['exam_history.member_id' => Yii::$app->user->identity->member_id]);
        $query->orderBy(['exam_history_id' => SORT_ASC]);
        return $query->all();
    }
    
    /*
     * Get total member join exam
     * 
     * Auth : 
     * Created : 20-06-2017
     */
    public function getTotalMemberJoinByExam($examId, $flagCount = true) {
        $query = new \yii\db\Query();
        $query->select(['exam_history.*'])
                ->from('exam_history');
        $query->where(['exam_history.exam_id' => $examId]);
        if (!$flagCount) {
            return $query->all();
        }
        return $query->count();
    }
    
    /*
     * Get total ans correct
     * 
     * Auth : 
     * Created : 20-06-2017
     */
    public function getTotalAnsCorrectByExam($examId) {
        $query = new \yii\db\Query();
        $query->select(['exam_history.*'])
                ->from('exam_history');
        $query->where(['exam_history.exam_id' => $examId]);
        $sum = $query->sum('total_correct');
        return $sum;
    }
    
    /*
     * Get rank exam
     * 
     * Auth : 
     * Created : 21-06-2017
     */
    public function getRankExam($examHistoryId, $examId) {
        $connection = Yii::$app->getDb();
        $sql = 'SELECT FIND_IN_SET( total_correct, ( SELECT GROUP_CONCAT( total_correct ORDER BY total_correct DESC ) FROM exam_history WHERE exam_id = ' . $examId. ') ) AS rank';
        $sql .= ' FROM exam_history';
        $sql .= ' WHERE exam_history_id = '.$examHistoryId ;
        return $connection->createCommand($sql)->queryAll();
    }
}
