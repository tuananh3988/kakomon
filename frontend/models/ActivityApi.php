<?php

namespace frontend\models;

use Yii;
use yii\base\Model;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use common\models\Category;
use common\models\ActivitySumary;
use common\models\Activity;
use common\models\Member;
/**
 * ContactForm is the model behind the contact form.
 */
class ActivityApi extends \yii\db\ActiveRecord
{
    public $category_main_id;
    public $type;
    
    const SCENARIO_DETAIL_SUMMARY = 'detail-summary';
    const SCENARIO_DETAIL_ACTIVITY = 'deail-activity';
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'activity';
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['category_main_id'], 'required', 'on' => self::SCENARIO_DETAIL_SUMMARY],
            [['category_main_id', 'type'], 'integer'],
            ['category_main_id', 'validateMainCategory', 'on' => self::SCENARIO_DETAIL_SUMMARY],
            [['member_id'], 'required', 'on' => self::SCENARIO_DETAIL_ACTIVITY],
            [['member_id'], 'validateMemberId', 'on' => self::SCENARIO_DETAIL_ACTIVITY],
            [['member_id', 'category_main_id', 'type', 'created_date', 'updated_date'], 'safe'],
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'category_main_id' => 'Category Main Id',
            'type' => 'Type',
            'member_id' => 'Member Id'
        ];
    }
    
    /*
     * Validate quiz id
     * 
     * Auth :
     * Create : 22-03-2017
     * 
     */
    
    public function validateMemberId($attribute)
    {
        if (!$this->hasErrors()) {
            $memberDetail = Member::findOne(['member_id' => $this->$attribute]);
            if (!$memberDetail) {
                $this->addError($attribute, \Yii::t('app', 'data not exist', ['attribute' => $this->attributeLabels()[$attribute]]));
            }
        }
    }
    
    /*
     * Validate quiz id
     * 
     * Auth :
     * Create : 22-03-2017
     * 
     */
    
    public function validateMainCategory($attribute)
    {
        if (!$this->hasErrors()) {
            $quizDetail = Category::findOne(['cateory_id' => $this->$attribute, 'parent_id' => 0]);
            if (!$quizDetail) {
                $this->addError($attribute, \Yii::t('app', 'data not exist', ['attribute' => $this->attributeLabels()[$attribute]]));
            }
        }
    }
    
    /*
     * 
     * Auth :
     * Created : 22-03-2017
     */
    
    public function getListCommnetByCategory($limit, $offset ,$flag = false){
        $query = new \yii\db\Query();
        $query->select(['activity.*', 'quiz.question','activity_sumary_like.total AS total_like' , 'activity_sumary_dis_like.total AS total_dis_like'])
                ->from('quiz');
        $query->join('INNER JOIN', 'activity', 'quiz.quiz_id = activity.quiz_id');
        $query->join('LEFT JOIN', 'activity_sumary as activity_sumary_like', 'activity_sumary_like.activity_id = activity.activity_id AND activity_sumary_like.type = '. ActivitySumary::TYPE_LIKE);
        $query->join('LEFT JOIN', 'activity_sumary as activity_sumary_dis_like', 'activity_sumary_dis_like.activity_id = activity.activity_id AND activity_sumary_like.type = '. ActivitySumary::TYPE_LIKE);
        $query->where(['=', 'quiz.category_main_id', $this->category_main_id]);
        $query->andWhere(['=', 'activity.type', Activity::TYPE_COMMENT]);
        $query->andWhere(['=', 'activity.member_id', Yii::$app->user->identity->member_id]);
        $query->andWhere(['=', 'activity.status', Activity::STATUS_ACTIVE]);
        if ($flag) {
            return $query->count();
        }
        $query->offset($offset);
        $query->limit($limit);
        return $query->all();
    }
    
    /*
     * 
     * Auth :
     * Created : 22-03-2017
     */
    
    public function getListQuizNotDoingByCategory($limit, $offset ,$flag = false){
        $query = new \yii\db\Query();
        $query->select(['activity.*', 'quiz.question','activity_sumary_like.total AS total_like' , 'activity_sumary_dis_like.total AS total_dis_like'])
                ->from('quiz');
        $query->join('INNER JOIN', 'member_quiz_history', 'quiz.quiz_id = member_quiz_history.quiz_id');
        $query->join('INNER JOIN', 'activity', 'quiz.quiz_id = activity.quiz_id');
        $query->join('LEFT JOIN', 'activity_sumary as activity_sumary_like', 'activity_sumary_like.activity_id = activity.activity_id AND activity_sumary_like.type = '. ActivitySumary::TYPE_LIKE);
        $query->join('LEFT JOIN', 'activity_sumary as activity_sumary_dis_like', 'activity_sumary_dis_like.activity_id = activity.activity_id AND activity_sumary_like.type = '. ActivitySumary::TYPE_LIKE);
        $query->where(['=', 'quiz.category_main_id', $this->category_main_id]);
        $query->andWhere(['=', 'activity.type', Activity::TYPE_COMMENT]);
        $query->andWhere(['=', 'activity.member_id', Yii::$app->user->identity->member_id]);
        $query->andWhere(['=', 'activity.status', Activity::STATUS_ACTIVE]);
        $query->andWhere(['NOT IN','quiz_id',  MemberQuizHistory::find()->select('quiz_id')->where(['member_id' => Yii::$app->user->identity->member_id, 'correct_flag' => MemberQuizHistory::FLAG_CORRECT_CORRECT, 'last_ans_flag' => MemberQuizHistory::FLAG_ANS_LAST])->asArray()->all()]);
        $query->andWhere(['NOT IN','quiz_id',  MemberQuizHistory::find()->select('quiz_id')->where(['member_id' => Yii::$app->user->identity->member_id, 'correct_flag' => MemberQuizHistory::FLAG_CORRECT_INCORRECT, 'last_ans_flag' => MemberQuizHistory::FLAG_ANS_LAST])->asArray()->all()]);
        if ($flag) {
            return $query->count();
        }
        $query->offset($offset);
        $query->limit($limit);
        return $query->all();
    }
    
    /*
     * 
     * Auth :
     * Created : 22-03-2017
     */
    
    public function getListActivityForMember($limit, $offset ,$flag = false){
        $query = new \yii\db\Query();
        $query->select(['activity.*', 'quiz.question', 'quiz.category_main_id', 'main_category.name as main_name',
                'sub_category.name as sub_name', 'quiz.category_a_id', 'member.name as name_meber'])
                ->from('activity');
        $query->join('INNER JOIN', 'quiz', 'quiz.quiz_id = activity.quiz_id');
        $query->join('INNER JOIN', 'member', 'activity.member_id = member.member_id');
        $query->join('INNER JOIN', 'category as main_category', 'quiz.category_main_id = main_category.cateory_id');
        $query->join('LEFT JOIN', 'category as sub_category', 'quiz.category_a_id = sub_category.cateory_id');
        $query->andWhere(['=', 'activity.member_id', $this->member_id]);
        $query->andWhere(['=', 'activity.status', Activity::STATUS_ACTIVE]);
        if ($flag) {
            return $query->count();
        }
        $query->offset($offset);
        $query->limit($limit);
        return $query->all();
    }
    
    
}