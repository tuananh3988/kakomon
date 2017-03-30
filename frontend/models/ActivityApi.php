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
use common\models\MemberQuizActivity;
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
        $query->join('LEFT JOIN', 'activity_sumary AS activity_sumary_like', 'activity_sumary_like.activity_id = activity.activity_id AND activity_sumary_like.type = '. ActivitySumary::TYPE_LIKE);
        $query->join('LEFT JOIN', 'activity_sumary AS activity_sumary_dis_like', 'activity_sumary_dis_like.activity_id = activity.activity_id AND activity_sumary_dis_like.type = '. ActivitySumary::TYPE_DIS_LIKE);
        $query->where(['=', 'quiz.category_main_id', $this->category_main_id]);
        $query->andWhere(['=', 'activity.type', Activity::TYPE_COMMENT]);
        $query->andWhere(['=', 'activity.member_id', Yii::$app->user->identity->member_id]);
        $query->andWhere(['=', 'activity.status', Activity::STATUS_ACTIVE]);
        if ($flag) {
            return $query->count();
        }
        $query->offset($offset);
        $query->limit($limit);
        $query->orderBy(['activity.activity_id' => SORT_DESC]);
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
        $query->join('LEFT JOIN', 'activity_sumary as activity_sumary_dis_like', 'activity_sumary_dis_like.activity_id = activity.activity_id AND activity_sumary_dis_like.type = '. ActivitySumary::TYPE_LIKE);
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
            'sub_category.name as sub_name', 'quiz.category_a_id', 'member.name as name_member', 'activity_sumary_like.total AS total_like', 'activity_sumary_dis_like.total AS total_dis_like', 
            'activity_is_like.activity_id AS isLike', 'activity_is_dis_like.activity_id AS isDisLike'])
                ->from('activity');
        $query->join('INNER JOIN', 'quiz', 'quiz.quiz_id = activity.quiz_id');
        $query->join('INNER JOIN', 'member', 'activity.member_id = member.member_id');
        $query->join('INNER JOIN', 'category as main_category', 'quiz.category_main_id = main_category.cateory_id');
        $query->join('LEFT JOIN', 'activity_sumary as activity_sumary_like', 'activity_sumary_like.activity_id = activity.activity_id AND activity_sumary_like.type = ' . ActivitySumary::TYPE_LIKE );
        $query->join('LEFT JOIN', 'activity_sumary as activity_sumary_dis_like', 'activity_sumary_dis_like.activity_id = activity.activity_id AND activity_sumary_dis_like.type = ' . ActivitySumary::TYPE_DIS_LIKE );
        $query->join('LEFT JOIN', 'category as sub_category', 'quiz.category_a_id = sub_category.cateory_id');
        $query->join('LEFT JOIN', 'activity AS activity_is_like', 'activity_is_like.relate_id = activity.activity_id AND activity_is_like.member_id = '. $this->member_id . ' AND activity_is_like.type = ' .Activity::TYPE_LIKE . ' AND activity_is_like.status = ' . Activity::STATUS_ACTIVE);
        $query->join('LEFT JOIN', 'activity AS activity_is_dis_like', 'activity_is_dis_like.relate_id = activity.activity_id AND activity_is_dis_like.member_id = '. $this->member_id . ' AND activity_is_dis_like.type = ' .Activity::TYPE_DISLIKE . ' AND activity_is_dis_like.status = ' . Activity::STATUS_ACTIVE);
        $query->andWhere(['=', 'activity.member_id', $this->member_id]);
        $query->andWhere(['=', 'activity.status', Activity::STATUS_ACTIVE]);
        if ($flag) {
            return $query->count();
        }
        $query->offset($offset);
        $query->limit($limit);
        $query->orderBy(['activity.activity_id' => SORT_DESC]);
        return $query->all();
    }
    
    
    /*
     * Delete activity
     * 
     * Auth :
     * Created : 29-03-2017
     */
    
    public static function deleteActivity($activityId, $quizId){
        $activity = Activity::findOne(['activity_id' => $activityId]);
        $type = $activity->type;
        self::updateActivitySumary($activityId);
        switch ($type) {
            case 1:
                $listMember = Activity::find()->select('member_id')->where(['relate_id' => $activityId, 'status' => Activity::STATUS_ACTIVE])->groupBy(['member_id'])->column();
                //update table activity
                Activity::updateAll(['status' => Activity::STATUS_DELETE], 'relate_id = ' . $activityId);
                if (count($listMember) > 0) {
                    if (!in_array(Yii::$app->user->identity->member_id, $listMember)) {
                        $listMember[] = Yii::$app->user->identity->member_id;
                    }
                    self::updateMemberQuizActivity($listMember, $quizId);
                }
                break;
            case 2:
                //find all reply
                $activityReply = Activity::find()->where(['relate_id' => $activity->activity_id, 'type' => Activity::TYPE_REPLY, 'status' => Activity::STATUS_ACTIVE])->all();
                
                $listMemberHelp = Activity::find()->select('member_id')->where(['relate_id' => $activityId, 'status' => Activity::STATUS_ACTIVE])->groupBy(['member_id'])->column();
                //update table activity
                Activity::updateAll(['status' => Activity::STATUS_DELETE], 'relate_id = ' . $activityId);
                if (count($activityReply) > 0){
                    foreach ($activityReply as $key => $value) {
                        $listMemberReply = Activity::find()->select('member_id')->where(['relate_id' => $value['activity_id'], 'status' => Activity::STATUS_ACTIVE])->groupBy(['member_id'])->column();
                        //update table activity reply
                        Activity::updateAll(['status' => Activity::STATUS_DELETE], 'relate_id = ' . $value['activity_id']);
                        self::updateActivitySumary($value['activity_id']);
                        if (count($listMemberReply) > 0) {
                            self::updateMemberQuizActivity($listMemberReply, $quizId);
                        }
                    }
                }
                if (count($listMemberHelp) > 0) {
                    if (!in_array(Yii::$app->user->identity->member_id, $listMemberHelp)) {
                        $listMemberHelp[] = Yii::$app->user->identity->member_id;
                    }
                    self::updateMemberQuizActivity($listMemberHelp, $quizId);
                }
                break;
            case 3:
                $listMember = Activity::find()->select('member_id')->where(['relate_id' => $activityId, 'status' => Activity::STATUS_ACTIVE])->groupBy(['member_id'])->column();
                //update table activity
                Activity::updateAll(['status' => Activity::STATUS_DELETE], 'relate_id = ' . $activityId);
                if (count($listMember) > 0) {
                    if (!in_array(Yii::$app->user->identity->member_id, $listMember)) {
                        $listMember[] = Yii::$app->user->identity->member_id;
                    }
                    self::updateMemberQuizActivity($listMember, $quizId);
                }
                break;
            case 4:
                break;
            case 5:
                break;
            default:
                break;
        }
        return true;
    }
    
    /*
     * Update table MemberQuizActivity
     * 
     * Auth : 
     * Created : 29-03-2017
     * 
     */
    public static function updateMemberQuizActivity($data, $quizId){
        foreach ($data as $key => $value) {
            //update table member_quiz_activity
            if (count(Activity::checkActivityForMember($quizId, (int)$value)) == 0) {
                $memberQuizActivity = MemberQuizActivity::findOne(['member_id' => (int)$value, 'quiz_id' => $quizId]);
                if ($memberQuizActivity) {
                    $memberQuizActivity->delete_flag = MemberQuizActivity::DELETE_DELETE;
                    $memberQuizActivity->save();
                }
            }
        }
        return true;
    }
    
    /*
     * Update table ActivitySumary
     * 
     * Auth : 
     * Created : 29-03-2017
     * 
     */
    public static function updateActivitySumary($activityId){
        $activitySumaryLike = ActivitySumary::findOne(['activity_id' => $activityId, 'type' => ActivitySumary::TYPE_LIKE]);
        $activitySumaryDisLike = ActivitySumary::findOne(['activity_id' => $activityId, 'type' => ActivitySumary::TYPE_DIS_LIKE]);
        if ($activitySumaryLike) {
            $activitySumaryLike->total = 0;
            $activitySumaryLike->save();
        }
        if ($activitySumaryDisLike) {
            $activitySumaryDisLike->total = 0;
            $activitySumaryDisLike->save();
        }
        
        return true;
    }
    
    
    /*
     * 
     * Auth :
     * Created : 22-03-2017
     */
    
    public function getListLikeByCategory($limit, $offset ,$flag = false){
        $query = new \yii\db\Query();
        $query->select(['activity_like.activity_id', 'quiz.quiz_id','activity_like.content AS content', 'activity_like.type', 'quiz.question','activity_sumary_like.total AS total_like' ,
            'activity_sumary_dis_like.total AS total_dis_like', 'member.name' ,'member.member_id'])
                ->from('quiz');
        $query->join('INNER JOIN', 'activity', 'quiz.quiz_id = activity.quiz_id');
        $query->join('INNER JOIN', 'activity AS activity_like', 'activity_like.activity_id = activity.relate_id');
        $query->join('INNER JOIN', 'member', 'member.member_id = activity_like.member_id');
        $query->join('LEFT JOIN', 'activity_sumary AS activity_sumary_like', 'activity_sumary_like.activity_id = activity_like.activity_id AND activity_sumary_like.type = '. ActivitySumary::TYPE_LIKE);
        $query->join('LEFT JOIN', 'activity_sumary AS activity_sumary_dis_like', 'activity_sumary_dis_like.activity_id = activity_like.activity_id AND activity_sumary_dis_like.type = '. ActivitySumary::TYPE_DIS_LIKE);
        $query->where(['=', 'quiz.category_main_id', $this->category_main_id]);
        $query->andWhere(['=', 'activity.type', Activity::TYPE_LIKE]);
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
    
    public function getListNasiByCategory($limit, $offset ,$flag = false){
        $query = new \yii\db\Query();
        $query->select(['activity_like.activity_id', 'quiz.quiz_id','activity_like.content AS content', 'activity_like.type', 'quiz.question','activity_sumary_like.total AS total_like' ,
            'activity_sumary_dis_like.total AS total_dis_like', 'member.name' ,'member.member_id'])
                ->from('quiz');
        $query->join('INNER JOIN', 'activity', 'quiz.quiz_id = activity.quiz_id');
        $query->join('INNER JOIN', 'activity AS activity_like', 'activity_like.activity_id = activity.relate_id');
        $query->join('INNER JOIN', 'member', 'member.member_id = activity_like.member_id');
        $query->join('LEFT JOIN', 'activity_sumary AS activity_sumary_like', 'activity_sumary_like.activity_id = activity_like.activity_id AND activity_sumary_like.type = '. ActivitySumary::TYPE_LIKE);
        $query->join('LEFT JOIN', 'activity_sumary AS activity_sumary_dis_like', 'activity_sumary_dis_like.activity_id = activity_like.activity_id AND activity_sumary_dis_like.type = '. ActivitySumary::TYPE_DIS_LIKE);
        $query->where(['=', 'quiz.category_main_id', $this->category_main_id]);
        $query->andWhere(['=', 'activity.type', Activity::TYPE_LIKE]);
        $query->andWhere(['=', 'activity.member_id', Yii::$app->user->identity->member_id]);
        $query->andWhere(['=', 'activity.status', Activity::STATUS_ACTIVE]);
        $query->andWhere(['NOT IN','quiz_id',  MemberQuizActivity::find()->select('quiz_id')->where(['member_id' => Yii::$app->user->identity->member_id, 'delete_flag' => MemberQuizActivity::DELETE_ACTIVE])->asArray()->all()]);
        if ($flag) {
            return $query->count();
        }
        $query->offset($offset);
        $query->limit($limit);
        return $query->all();
    }
}