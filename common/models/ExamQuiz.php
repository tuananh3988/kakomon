<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\data\ActiveDataProvider;

/**
 * This is the model class for table "exam_quiz".
 *
 * @property integer $exam_quiz_id
 * @property integer $exam_id
 * @property integer $quiz_id
 * @property string $created_date
 * @property string $updated_date
 */
class ExamQuiz extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'exam_quiz';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['exam_id', 'quiz_id'], 'required'],
            [['exam_id', 'quiz_id'], 'integer'],
            [['created_date', 'updated_date'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'exam_quiz_id' => 'Exam Quiz ID',
            'exam_id' => 'Exam ID',
            'quiz_id' => 'Quiz ID',
            'created_date' => 'Created Date',
            'updated_date' => 'Updated Date',
        ];
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
    
    /*
     * List quiz detail
     * 
     * Auth : 
     * Create : 19-02-2017     
     */
    public function listQuiz($examId){
        $query = new \yii\db\Query();
        $query->select('exam_quiz.*, quiz.question')
                ->from('exam_quiz');
        $query->join('INNER JOIN', 'quiz', 'quiz.quiz_id = exam_quiz.quiz_id');
        $query->andWhere(['exam_quiz.exam_id' => $examId]);
        $query->andWhere(['quiz.delete_flag' => 0]);
        $query->andWhere(['quiz.type' => 3]);
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => Yii::$app->params['pageSize']
            ],
            'sort' => [
                'defaultOrder' => [
                    'exam_quiz_id' => SORT_DESC,
                    'created_date' => SORT_DESC,
                ]
            ],
        ]);
        $dataProvider->sort->attributes['exam_quiz_id'] = [
            'desc' => ['exam_quiz.exam_quiz_id' => SORT_DESC],
            'asc' => ['exam_quiz.exam_quiz_id' => SORT_ASC],
        ];
        $dataProvider->sort->attributes['created_date'] = [
            'desc' => ['exam_quiz.created_date' => SORT_DESC],
            'asc' => ['exam_quiz.created_date' => SORT_ASC],
        ];
        return $dataProvider;
    }
}
