<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use common\models\ExamQuiz;
/**
 * This is the model class for table "member_quiz_history".
 *
 * @property integer $member_quiz_history_id
 * @property integer $quiz_id
 * @property integer $exam_id
 * @property integer $member_id
 * @property string $answer
 * @property integer $correct_flag
 * @property integer $time
 * @property string $created_date
 */
class MemberQuizHistory extends \yii\db\ActiveRecord
{
    
    const FLAG_CORRECT_CORRECT = 1;
    const FLAG_CORRECT_INCORRECT = 2;
    const FLAG_CORRECT_NOT_DOING = 3;
    
    const FLAG_ANS_LAST = 0;
    const FLAG_ANS_BEFORE = 1;
    
    const LIMIT_ANS = 2;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'member_quiz_history';
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
            [['quiz_id', 'member_id', 'answer'], 'required'],
            [['quiz_id', 'exam_id', 'member_id', 'correct_flag', 'time'], 'integer'],
            [['created_date'], 'safe'],
            [['answer'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'member_quiz_history_id' => 'Member Quiz History ID',
            'quiz_id' => 'Quiz ID',
            'exam_id' => 'Exam ID',
            'member_id' => 'Member ID',
            'answer' => 'Answer',
            'correct_flag' => 'Correct Flag',
            'time' => 'Time',
            'created_date' => 'Created Date',
        ];
    }
    
    /*
     * get 2 record ans last
     * 
     * Auth : 
     * Createt: 16-03-2017
     */
    
    public static function getTwoRecordAnsWithMember($quizId){
        $query = new \yii\db\Query();
        $query->select(['member_quiz_history.*'])
                ->from('member_quiz_history');
        $query->where(['=', 'member_quiz_history.quiz_id', $quizId]);
        $query->andWhere(['=', 'member_quiz_history.member_id', Yii::$app->user->identity->member_id]);
        $query->andWhere(['!=', 'member_quiz_history.correct_flag',  self::FLAG_CORRECT_NOT_DOING]);
        $query->orderBy(['member_quiz_history_id' =>SORT_DESC]);
        $query->limit(self::LIMIT_ANS);
        return $query->all();
    }
    
    /*
     * Get list ans wrong for member
     * 
     * Auth : 
     * Create : 18-03-2017
     */
    
    public function getListAnsWrongForMember(){
        $query = new \yii\db\Query();
        $query->select(['member_quiz_history.*', 'quiz.*'])
                ->from('member_quiz_history');
        $query->join('INNER JOIN', 'quiz', 'quiz.quiz_id = member_quiz_history.quiz_id');
        $query->where(['=', 'quiz.delete_flag', Quiz::QUIZ_ACTIVE]);
        $query->andWhere(['=', 'member_quiz_history.member_id', Yii::$app->user->identity->member_id]);
        $query->andWhere(['=', 'member_quiz_history.correct_flag', self::FLAG_CORRECT_INCORRECT]);
        $query->andWhere(['=', 'member_quiz_history.last_ans_flag', self::FLAG_ANS_LAST]);
        return $query->all();
    }
    
    /*
     * Get list ans for member
     * 
     * Auth : 
     * Create : 18-03-2017
     */
    
    public function getListAnsForMember(){
        $quizIdCorrect = MemberQuizHistory::find()->select('quiz_id')->where(['member_id' => Yii::$app->user->identity->member_id, 'correct_flag' => MemberQuizHistory::FLAG_CORRECT_CORRECT, 'last_ans_flag' => MemberQuizHistory::FLAG_ANS_LAST])->asArray()->all();
        $quizIdInCorrect = MemberQuizHistory::find()->select('quiz_id')->where(['member_id' => Yii::$app->user->identity->member_id, 'correct_flag' => MemberQuizHistory::FLAG_CORRECT_INCORRECT, 'last_ans_flag' => MemberQuizHistory::FLAG_ANS_LAST])->asArray()->all();
        $quizId = array_merge($quizIdCorrect,$quizIdInCorrect);
        
        $query = new \yii\db\Query();
        $query->select(['quiz.*'])
                ->from('quiz');
        $query->where(['=', 'quiz.delete_flag', Quiz::QUIZ_ACTIVE]);
        $query->andWhere(['IN','quiz_id',  $quizId]);
        $query->andWhere(['=', 'quiz.type', Quiz::TYPE_NORMAL]);
        return $query->all();
    }
    /*
     * Get total ans correct for exam
     * 
     * Auth : 
     * Create : 20-06-2017
     */
    public function getTotalAnsCorrectByExam($examId, $contestTimes, $categoryId = null, $memberId = null) {
        $query = new \yii\db\Query();
        $query->select(['member_quiz_history.member_quiz_history_id'])
                ->from('member_quiz_history');
        //check for category
        if ($categoryId) {
            $query->join('INNER JOIN', 'quiz', 'quiz.quiz_id = member_quiz_history.quiz_id');
        }
        $query->where(['=', 'exam_id', $examId]);
        $query->andWhere(['=','correct_flag', self::FLAG_CORRECT_CORRECT]);
        if ($memberId) {
            $query->andWhere(['=','member_id',  $memberId]);
        } else {
            $query->andWhere(['=','member_id',  Yii::$app->user->identity->member_id]);
        }
        $query->andWhere(['=', 'contest_times', $contestTimes]);
        $query->andWhere(['=', 'last_ans_flag', MemberQuizHistory::FLAG_ANS_LAST]);
        if ($categoryId) {
            $query->andWhere(['=', 'quiz.category_main_id', $categoryId]);
        }
        return $query->count();
    }
    
    /*
     * Get total ans not correct for exam
     * 
     * Auth :
     * Created : 20-06-2017
     */
    public function getTotalNotAnsByExam($examId, $contestTimes) {
        $quizIdCorrect = MemberQuizHistory::find()->select('quiz_id')->where(['member_id' => Yii::$app->user->identity->member_id, 'correct_flag' => MemberQuizHistory::FLAG_CORRECT_CORRECT, 'last_ans_flag' => MemberQuizHistory::FLAG_ANS_LAST, 'exam_id' => $examId, 'contest_times' => $contestTimes])->indexBy('quiz_id')->column();
        $quizIdInCorrect = MemberQuizHistory::find()->select('quiz_id')->where(['member_id' => Yii::$app->user->identity->member_id, 'correct_flag' => MemberQuizHistory::FLAG_CORRECT_INCORRECT, 'last_ans_flag' => MemberQuizHistory::FLAG_ANS_LAST, 'exam_id' => $examId, 'contest_times' => $contestTimes])->indexBy('quiz_id')->column();
        $quizId = array_merge($quizIdCorrect,$quizIdInCorrect);
        $query = new \yii\db\Query();
        $query->select(['exam_quiz.exam_quiz_id'])
                ->from('exam_quiz');
        $query->where(['=', 'exam_quiz.exam_id', $examId]);
        $total = $query->count();
        return (int)($total- count($quizId));
    }
    
    /*
     * Get total not ans
     * 
     * Auth : 
     * Created : 
     */
    public function getTotalNotAnsByExamAndCategory($examId, $contestTimes, $categoryId) {
        $modelExamQuiz = new ExamQuiz();
        $totalQuiz = $modelExamQuiz->getCountQuizByIdExam($examId, $categoryId);
        $query = new \yii\db\Query();
        $query->select(['member_quiz_history.member_quiz_history_id'])
                ->from('member_quiz_history');
        $query->join('INNER JOIN', 'quiz', 'quiz.quiz_id = member_quiz_history.quiz_id');
        $query->where(['=', 'exam_id', $examId]);
        $query->andWhere(['=','member_id', Yii::$app->user->identity->member_id]);
        $query->andWhere(['=', 'contest_times', $contestTimes]);
        $query->andWhere(['=', 'last_ans_flag', MemberQuizHistory::FLAG_ANS_LAST]);
        $query->andWhere(['=', 'quiz.category_main_id', $categoryId]);
        $query->andWhere([
            'OR' ,
            ['=','correct_flag', self::FLAG_CORRECT_CORRECT],
            ['=','correct_flag', self::FLAG_CORRECT_INCORRECT]
        ]);
        $totalAns = $query->count();
        return (int)($totalQuiz - $totalAns);
    }
}
