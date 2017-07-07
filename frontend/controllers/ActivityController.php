<?php
namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\QueryParamAuth;
use common\components\Utility;
use common\models\Activity;
use common\models\Category;
use common\models\Quiz;
use common\models\MemberQuizActivity;
use common\models\ActivitySumary;
use frontend\models\Like;
use frontend\models\Comment;
use frontend\models\Help;
use frontend\models\Reply;
use frontend\models\ActivityApi;
use common\models\MemberCategoryTime;


/**
 * Site controller
 */
class ActivityController extends Controller
{
    
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'like' => ['post'],
                    'dislike' => ['post'],
                    'addComment' => ['post'],
                    'edit' => ['post'],
                    'deleteComment' => ['post'],
                    'listComment' => ['get'],
                    'addHelp' => ['post'],
                    'deleteHelp' => ['post'],
                    'listHelp' => ['get'],
                    'addReply' => ['post'],
                    'deleteReply' => ['post'],
                    'listReply' => ['get'],
                    'timeline' => ['get'],
                    'home' => ['get'],
                    'summary' => ['get'],
                    'mySummary' => ['get'],
                    'memberSummary' => ['get'],
                    'detailSummary' => ['get']
                ],
            ],
            'authenticator' => [
                'class' => CompositeAuth::className(),
                'except' => [],
                'authMethods' => [
                    QueryParamAuth::className(),
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }
    
    /*
     * Action Like
     * 
     * Auth : 
     * Create : 01-03-2017
     */
    
    public function actionLike()
    {
        $request = Yii::$app->request;
        $dataPost = $request->post();
        $modelLike = new Like();
        $modelLike->scenario  = Like::SCENARIO_LIKE;
        $modelLike->setAttributes($dataPost);
        if (!$modelLike->validate()) {
            return [
                    'status' => 400,
                    'messages' => $modelLike->errors
                ];
        }
        $activityDetail = Activity::findOne(['activity_id' => $modelLike->activity_id]);
        $activityDetailDisLike = Activity::findOne(['member_id' => Yii::$app->user->identity->member_id, 'type' => Activity::TYPE_DISLIKE,'status' => Activity::STATUS_ACTIVE, 'relate_id' => $modelLike->activity_id]);
        $activityDetailOld = Activity::find()->where(['member_id' => Yii::$app->user->identity->member_id, 'type' => Activity::TYPE_LIKE, 'relate_id' => $modelLike->activity_id])->orderBy(['activity_id' => SORT_DESC])->one();
        
        //insert new record and update status record like
        if (!$activityDetailOld || ($activityDetailOld && $activityDetailOld->status == Activity::STATUS_DELETE && $activityDetailOld->updated_date != null && $modelLike->status != Activity::STATUS_DELETE)) {
            $dataSave = $modelLike->saveLike($activityDetail, $activityDetailDisLike);
            //return error
            if (!$dataSave) {
                return [
                    'status' => 400,
                    'messages' => 'System error'
                ];
            }
            //return success
            return  [
                'status' => 200,
                'data' => [
                    'activity_id' => $dataSave
                ]
            ];
            
        } else {
            //update status if status post diff status old
            if ($activityDetailOld->status != $modelLike->status) {
                if (!$modelLike->updateLikeOrDisLike($activityDetailOld, $activityDetail , 1)) {
                    return [
                        'status' => 400,
                        'messages' => 'System error'
                    ];
                }
                return  [
                    'status' => 200
                ];
            }
            
            return  [
                'status' => 200
            ];
        }
    }
    
    
    /*
     * Action Dis Like
     * 
     * Auth : 
     * Create : 01-03-2017
     */
    
    public function actionDislike()
    {
        $request = Yii::$app->request;
        $dataPost = $request->post();
        $modelLike = new Like();
        $modelLike->scenario  = Like::SCENARIO_DIS_LIKE;
        $modelLike->setAttributes($dataPost);
        if (!$modelLike->validate()) {
            return [
                    'status' => 400,
                    'messages' => $modelLike->errors
                ];
        }
        $activityDetail = Activity::findOne(['activity_id' => $modelLike->activity_id]);
        $activityDetailLike = Activity::findOne(['member_id' => Yii::$app->user->identity->member_id, 'type' => Activity::TYPE_LIKE,'status' => Activity::STATUS_ACTIVE, 'relate_id' => $modelLike->activity_id]);
        $activityDetailOld = Activity::find()->where(['member_id' => Yii::$app->user->identity->member_id, 'type' => Activity::TYPE_DISLIKE, 'relate_id' => $modelLike->activity_id])->orderBy(['activity_id' => SORT_DESC])->one();
        
        //insert new record and update status record like
        if (!$activityDetailOld || ($activityDetailOld && $activityDetailOld->status == Activity::STATUS_DELETE && $activityDetailOld->updated_date != null && $modelLike->status != Activity::STATUS_DELETE)) {
            $dataSave = $modelLike->saveDisLike($activityDetail, $activityDetailLike);
            //return error
            if (!$dataSave) {
                return [
                    'status' => 400,
                    'messages' => 'System error'
                ];
            }
            //return success
            return  [
                'status' => 200,
                'data' => [
                    'activity_id' => $dataSave
                ]
            ];
        } else {
            //update status if status post diff status old
            if ($activityDetailOld->status != $modelLike->status) {
                if (!$modelLike->updateLikeOrDisLike($activityDetailOld, $activityDetail, 2)) {
                    return [
                        'status' => 400,
                        'messages' => 'System error'
                    ];
                }
                return  [
                    'status' => 200
                ];
            }
            return  [
                'status' => 200
            ];
        }
    }
    
    
    /*
     * Add comment
     * 
     * Auth : 
     * Create : 01-03-2017
     */
    
    public function actionAddComment()
    {
        $request = Yii::$app->request;
        $dataPost = $request->post();
        
        $modelComment = new Comment();
        $modelComment->setAttributes($dataPost);
        $modelComment->scenario  = Comment::SCENARIO_ADD_COMMENT;
        if (!$modelComment->validate()) {
            return [
                    'status' => 400,
                    'messages' => $modelComment->errors
                ];
        }
        //save data
        $modelComment->member_id = Yii::$app->user->identity->member_id;
        $modelComment->status = Activity::STATUS_ACTIVE;
        $modelComment->type = Activity::TYPE_COMMENT;
        //return error system
        if (!$modelComment->save()) {
            throw new \yii\base\Exception( "System error" );
        }
        //insert table member_quiz_activity
        $memberQuizActivity = MemberQuizActivity::findOne(['member_id' => Yii::$app->user->identity->member_id, 'quiz_id' => $modelComment->quiz_id]);
        if (!$memberQuizActivity) {
            $modelMemberQuizActivity = new MemberQuizActivity();
            $modelMemberQuizActivity->member_id = Yii::$app->user->identity->member_id;
            $modelMemberQuizActivity->quiz_id = $modelComment->quiz_id;
            $modelMemberQuizActivity->save();
        }

        return  [
            'status' => 200,
            'data' => [
                'activity_id' => $modelComment->activity_id
            ]
        ];
    }
    
    /*
     * Edit comment
     * 
     * Auth : 
     * Create : 01-03-2017
     */
    
    public function actionEdit()
    {
        $request = Yii::$app->request;
        $dataPost = $request->post();
        
        $modelActivity = new ActivityApi();
        $modelActivity->setAttributes($dataPost);
        $modelActivity->scenario  = ActivityApi::SCENARIO_EDIT_ACTIVITY;
        if (!$modelActivity->validate()) {
            return [
                    'status' => 400,
                    'messages' => $modelActivity->errors
                ];
        }
        $dataActivity = Comment::findOne(['activity_id' => $modelActivity->activity_id, 'type' => $modelActivity->type, 'member_id' => Yii::$app->user->identity->member_id, 'status' => Activity::STATUS_ACTIVE]);
        //save data
        $dataActivity->content = $modelActivity->content;
        //return error system
        if (!$dataActivity->save()) {
            throw new \yii\base\Exception( "System error" );
        }
        return  [
            'status' => 200,
            'data' => [
                'activity_id' => (int)$dataActivity->activity_id
            ]
        ];
    }
    
    /*
     * Delete comment
     * 
     * Auth : 
     * Create : 01-03-2017
     */
    
    public function actionDeleteComment()
    {
        $request = Yii::$app->request;
        $dataPost = $request->post();
        
        $modelComment = new Comment();
        $modelComment->setAttributes($dataPost);
        $modelComment->scenario  = Comment::SCENARIO_DELETE_COMMENT;
        if (!$modelComment->validate()) {
            return [
                    'status' => 400,
                    'messages' => $modelComment->errors
                ];
        }
        //update status
        $commentDetail = Comment::findOne(['activity_id' => $modelComment->activity_id, 'member_id' => Yii::$app->user->identity->member_id, 'type' => Activity::TYPE_COMMENT]);
        //return not found data
        if (!$commentDetail) {
            return [
                'status' => 204,
                'message' => \Yii::t('app', 'data not found')
            ];
        }
        //return error system
        if (!$modelComment->updateComment($commentDetail, $modelComment->activity_id, $commentDetail->quiz_id)) {
            throw new \yii\base\Exception( "System error" );
            
        }
        //return success
        return [
            'status' => 200
        ];
    }
    
    /*
     * List comment
     * 
     * Auth : 
     * Create : 04-03-2017
     */
    
    public function actionListComment()
    {
        $request = Yii::$app->request;
        $param = $request->queryParams;
        $limit = isset($param['limit']) ? $param['limit'] : Yii::$app->params['limit']['comment'];
        $offset = isset($param['offset']) ? $param['offset'] : Yii::$app->params['offset']['comment'];
        
        $modelComment = new Comment();
        $modelComment->setAttributes($param);
        $modelComment->scenario  = Comment::SCENARIO_LIST_COMMENT;
        if (!$modelComment->validate()) {
            return [
                    'status' => 400,
                    'messages' => $modelComment->errors
                ];
        }
        
        //return data
        $total = Comment::getTotalCommnetByQuizID($param['quiz_id']);
        $offsetReturn = Utility::renderOffset($total, $limit, $offset);
        return [
            'status' => 200,
            'count' => (int)$total,
            'offset' => $offsetReturn,
            'data' => Comment::renderListComment($param['quiz_id'], $limit, $offset)
            
        ];
    }
    
    /*
     * List reply
     * 
     * Auth : 
     * Create : 04-03-2017
     */
    
    public function actionListReply()
    {
        $request = Yii::$app->request;
        $param = $request->queryParams;
        $limit = isset($param['limit']) ? $param['limit'] : Yii::$app->params['limit']['reply'];
        $offset = isset($param['offset']) ? $param['offset'] : Yii::$app->params['offset']['reply'];
        
        $modelReply = new Reply();
        $modelReply->setAttributes($param);
        $modelReply->scenario  = Reply::SCENARIO_LIST_REPLY;
        if (!$modelReply->validate()) {
            return [
                    'status' => 400,
                    'messages' => $modelReply->errors
                ];
        }
        
        //return data
        $total = Reply::getTotalReplyByActivityId($param['activity_id']);
        $offsetReturn = Utility::renderOffset($total, $limit, $offset);
        return [
            'status' => 200,
            'count' => (int)$total,
            'offset' => $offsetReturn,
            'data' => Reply::renderListReply($param['activity_id'], $limit, $offset)
            
        ];
    }
    
    /*
     * List help
     * 
     * Auth : 
     * Create : 04-03-2017
     */
    
    public function actionListHelp()
    {
        $request = Yii::$app->request;
        $param = $request->queryParams;
        $limit = isset($param['limit']) ? $param['limit'] : Yii::$app->params['limit']['help'];
        $offset = isset($param['offset']) ? $param['offset'] : Yii::$app->params['offset']['help'];
        
        $modelHelp = new Help();
        $modelHelp->setAttributes($param);
        $modelHelp->scenario  = Help::SCENARIO_LIST_HELP;
        if (!$modelHelp->validate()) {
            return [
                    'status' => 400,
                    'messages' => $modelHelp->errors
                ];
        }
        $category = Quiz::getNameCategoryByQuizId($param['quiz_id']);
        //return data
        $total = Help::getTotalHelpByQuizId($param['quiz_id']);
        $offsetReturn = Utility::renderOffset($total, $limit, $offset);
        return [
            'status' => 200,
            'count' => (int)$total,
            'offset' => $offsetReturn,
            'main_name' => $category['main_name'],
            'cateory_id_main' => $category['cateory_id_main'],
            'sub_name' => $category['sub_name'],
            'cateory_id_sub' => $category['cateory_id_sub'],
            'data' => Help::renderListHelp($param['quiz_id'], $limit, $offset)
            
        ];
    }
    
    /*
     * Add help
     * 
     * Auth : 
     * Create : 01-03-2017
     */
    
    public function actionAddHelp()
    {
        $request = Yii::$app->request;
        $dataPost = $request->post();
        $modelHelp = new Help();
        $modelHelp->setAttributes($dataPost);
        $modelHelp->scenario  = Help::SCENARIO_ADD_HELP;
        if (!$modelHelp->validate()) {
            return [
                'status' => 400,
                'messages' => $modelHelp->errors
            ];
        }
        //save data
        $modelHelp->member_id = Yii::$app->user->identity->member_id;
        $modelHelp->status = Activity::STATUS_ACTIVE;
        $modelHelp->type = Activity::TYPE_HELP;
        //return system error
        if (!$modelHelp->save()) {
            throw new \yii\base\Exception( "System error" );
            
        }
        //return success
        return  [
            'status' => 200,
            'data' => [
                'activity_id' => $modelHelp->activity_id
            ]
        ];
    }
    
    /*
     * Delete help
     * 
     * Auth : 
     * Create : 01-03-2017
     */
    
    public function actionDeleteHelp()
    {
        $request = Yii::$app->request;
        $dataPost = $request->post();
        $modelHelp = new Help();
        $modelHelp->setAttributes($dataPost);
        $modelHelp->scenario  = Help::SCENARIO_DELETE_HELP;
        if (!$modelHelp->validate()) {
            return [
                    'status' => 400,
                    'messages' => $modelHelp->errors
                ];
        }
        //update status
        $helpDetail = Help::findOne(['activity_id' => $modelHelp->activity_id, 'member_id' => Yii::$app->user->identity->member_id, 'type' => Activity::TYPE_HELP]);
        //return system error
        if (!$modelHelp->updateHelp($helpDetail, $modelHelp->activity_id, $helpDetail->quiz_id)) {
            throw new \yii\base\Exception( "System error" );
        }
        //return success
        return [
            'status' => 200
        ];
    }
    
    
    /*
     * Add reply
     * 
     * Auth : 
     * Create : 01-03-2017
     */
    
    public function actionAddReply()
    {
        $request = Yii::$app->request;
        $dataPost = $request->post();
        $modelReply = new Reply();
        $modelReply->setAttributes($dataPost);
        $modelReply->scenario  = Reply::SCENARIO_ADD_REPLY;
        if (!$modelReply->validate()) {
            return [
                    'status' => 400,
                    'messages' => $modelReply->errors
                ];
        }
        $activityDetail = Activity::findOne(['activity_id' => $modelReply->activity_id]);
        //return system error
        $dataSave = $modelReply->addReply($activityDetail);
        if (!$dataSave) {
            throw new \yii\base\Exception( "System error" );
        }
        //return success
        return  [
            'status' => 200,
            'data' => [
                'activity_id' => $dataSave
            ]
        ];
    }
    
    /*
     * Delete Reply
     * 
     * Auth : 
     * Create : 01-03-2017
     */
    
    public function actionDeleteReply()
    {
        $request = Yii::$app->request;
        $dataPost = $request->post();
        $modelReply = new Reply();
        $modelReply->setAttributes($dataPost);
        $modelReply->scenario  = Reply::SCENARIO_DELETE_REPLY;
        if (!$modelReply->validate()) {
            return [
                    'status' => 400,
                    'messages' => $modelReply->errors
                ];
        }
        //update status
        $replyDetail = Reply::findOne(['activity_id' => $modelReply->activity_id, 'member_id' => Yii::$app->user->identity->member_id, 'type' => Activity::TYPE_REPLY]);
        //return system error
        if (!$modelReply->updateReply($replyDetail, $modelReply->activity_id, $replyDetail->quiz_id)) {
            throw new \yii\base\Exception( "System error" );
        }
        //return success
        return [
            'status' => 200
        ];
    }
    
    /*
     * List timeline
     * 
     * Auth : 
     * Create : 04-03-2017
     */
    
    public function actionTimeline()
    {
        $request = Yii::$app->request;
        $param = $request->queryParams;
        $limit = isset($param['limit']) ? $param['limit'] : Yii::$app->params['limit']['timeline'];
        $offset = isset($param['offset']) ? $param['offset'] : Yii::$app->params['offset']['timeline'];
        $categoryId = isset($param['category_id']) ? $param['category_id'] : null;
        $modelCategory = new Category();
        $listHelp = $modelCategory->getListTimelineHelp($categoryId);
        //return no data
        if (count($listHelp) == 0) {
            return [
                'status' => 204,
                'message' => \Yii::t('app', 'data not found')
            ];
        }
        
        //list data
        $data = [];
        foreach ($listHelp as $key => $value) {
            $data[] = [
                'quiz_id' => (int)$value['quiz_id'],
                'activity_id' => (int)$value['activity_id'],
                'content_activity' => $value['content_activity'],
                'question' => $value['question'],
                'sub_menu' => Quiz::renderListSubCat($value['category_a_id'], $value['category_b_id']),
                'cateory_id' => (int)$value['cateory_id'],
                'name_category' => $value['name_category'],
                'member_id' => (int)$value['member_id'],
                'name' => $value['name'],
                'created_date' => $value['created_date_activity'],
                'avatar' => Utility::getImage('member', $value['member_id'], null, true)
            ];
        }
        //return data
        $total = $modelCategory->getListTimelineHelp($categoryId, true);
        $offsetReturn = Utility::renderOffset($total, $limit, $offset);
        return [
            'status' => 200,
            'count' => (int)$total,
            'offset' => $offsetReturn,
            'data' => $data
            
        ];
    }
    
    
    /*
     * List timeline home
     * 
     * Auth : 
     * Create : 04-03-2017
     */
    
    public function actionHome()
    {
        $request = Yii::$app->request;
        $param = $request->queryParams;
        $limit = isset($param['limit']) ? $param['limit'] : Yii::$app->params['limit']['timeline'];
        $offset = isset($param['offset']) ? $param['offset'] : Yii::$app->params['offset']['timeline'];
        $categoryId = isset($param['category_id']) ? $param['category_id'] : null;
        $modelCategory = new Category();
        $listHelp = $modelCategory->getListTimelineHelp($categoryId, false, true);
        //return no data
        if (count($listHelp) == 0) {
            return [
                'status' => 204,
                'message' => \Yii::t('app', 'data not found')
            ];
        }
        //list data
        $data = [];
        foreach ($listHelp as $key => $value) {
            $data[] = [
                'quiz_id' => (int)$value['quiz_id'],
                'activity_id' => (int)$value['activity_id'],
                'content_activity' => $value['content_activity'],
                'question' => $value['question'],
                'sub_menu' => Quiz::renderListSubCat($value['category_a_id'], $value['category_b_id']),
                'cateory_id' => (int)$value['cateory_id'],
                'name_category' => $value['name_category'],
                'member_id' => (int)$value['member_id'],
                'name' => $value['name'],
                'created_date' => $value['created_date_activity'],
                'avatar' => Utility::getImage('member', $value['member_id'], null, true)
            ];
        }
        //return data
        $total = $modelCategory->getListTimelineHelp($categoryId, true);
        $offsetReturn = Utility::renderOffset($total, $limit, $offset);
        return [
            'status' => 200,
            'count' => (int)$total,
            'offset' => $offsetReturn,
            'data' => $data
            
        ];
    }
    
    /*
     * List sumary
     * 
     * Auth : 
     * Created : 22-03-2017
     */
    
    public function actionMySummary(){
        $request = Yii::$app->request;
        $param = $request->queryParams;
        $limit = isset($param['limit']) ? $param['limit'] : Yii::$app->params['limit']['sumary'];
        $offset = isset($param['offset']) ? $param['offset'] : Yii::$app->params['offset']['sumary'];
        $modelCategory = new Category();
        $listCategory = $modelCategory->getListCategoryForMember($limit, $offset);
        $total = $modelCategory->getListCategoryForMember($limit, $offset, true);
        $offsetReturn = Utility::renderOffset($total, $limit, $offset);
        $dataCat = [];
        
        if (count($listCategory) > 0) {
            foreach ($listCategory as $key => $value) {
                $dataCat[] = [
                    'category_id' => (int)$value['cateory_id'],
                    'category_name' => $value['name'],
                    'total_time_view' => (int)$value['total_time'],
                    'total_quiz' => (int)Quiz::getTotalQuizByCategory($value['cateory_id']),
                    'total_ans_quiz' => (int)Quiz::getTotalQuizAnsByCategory($value['cateory_id']),
//                    'total_comment' => (int)Activity::getTotalQuizActivityByCategory($value['cateory_id'], Activity::TYPE_COMMENT),
//                    'total_like' => (int)Activity::getTotalQuizActivityByCategory($value['cateory_id'], Activity::TYPE_LIKE),
//                    'total_nasi' => (int)Quiz::getTotalQuizNasiByCategory($value['cateory_id']),
                    'rate_activity_category' => $this->getRateActivityByCategory($value['cateory_id'])
                ];
            }
        }
        
        return [
            'status' => 200,
            'data' => [
                'count' => (int)$total,
                'offset' => $offsetReturn,
                'category' => $dataCat
            ]
        ];
    }
    
    /*
     * List sumary
     * 
     * Auth : 
     * Created : 22-03-2017
     */
    
    public function actionMemberActivity(){
        $request = Yii::$app->request;
        $param = $request->queryParams;
        $limit = isset($param['limit']) ? $param['limit'] : Yii::$app->params['limit']['sumary'];
        $offset = isset($param['offset']) ? $param['offset'] : Yii::$app->params['offset']['sumary'];
        $modelActivity = new ActivityApi();
        $modelActivity->scenario  = ActivityApi::SCENARIO_DETAIL_ACTIVITY;
        $modelActivity->setAttributes($param);
        if (!$modelActivity->validate()) {
            return [
                    'status' => 400,
                    'messages' => $modelActivity->errors
                ];
        }
        
        $listData = $modelActivity->getListActivityForMember($limit, $offset);
        $total = $modelActivity->getListActivityForMember($limit, $offset, true);
        $offsetReturn = Utility::renderOffset($total, $limit, $offset);
        $data = [];
        if (count($listData) == 0) {
            return [
                'status' => 204,
                'message' => \Yii::t('app', 'data not found')
            ];
        }
        $listLikeAndDisLike = [Activity::TYPE_LIKE, Activity::TYPE_DISLIKE];
        foreach ($listData as $key => $value) {
            $data[] = [
                'category_id' => (int)$value['category_main_id'],
                'category_name' => $value['main_name'],
                'sub_category_id' => ($value['category_a_id']) ? (int)$value['category_a_id'] : null,
                'sub_category_name' => $value['sub_name'],
                'type' => (int)$value['type'],
                'quiz_id' =>  (int)$value['quizId'],
                'activity_id' => (int)$value['activity_id'],
                'member_name' => (!in_array($value['type'], $listLikeAndDisLike)) ? $value['name_member'] : Activity::getInforNameByActivity($value['activity_id']),
                'content' => (!in_array($value['type'], $listLikeAndDisLike)) ? $value['content'] : Activity::getInforContentByActivity($value['activity_id']),
                'total_like' => (!in_array($value['type'], $listLikeAndDisLike)) ? (int)$value['total_like'] : Activity::getInforTotalLikeOrDisLikeByActivity($value['activity_id'], ActivitySumary::TYPE_LIKE),
                'total_dis_like' => (!in_array($value['type'], $listLikeAndDisLike)) ? (int)$value['total_dis_like'] : Activity::getInforTotalLikeOrDisLikeByActivity($value['activity_id'], ActivitySumary::TYPE_DIS_LIKE),
                'isLike' => (!in_array($value['type'], $listLikeAndDisLike)) ? (($value['isLike']) ? true : false) : Activity::getInforLikeOrDisLikeByActivity($value['activity_id'], $modelActivity->member_id, Activity::TYPE_LIKE),
                'isDisLike' => (!in_array($value['type'], $listLikeAndDisLike)) ? (($value['isDisLike']) ? true : false) : Activity::getInforLikeOrDisLikeByActivity($value['activity_id'], $modelActivity->member_id, Activity::TYPE_DISLIKE),
                'created_date' => $value['created_date']
            ];
        }
        
        return [
            'status' => 200,
            'data' => [
                'count' => (int)$total,
                'offset' => $offsetReturn,
                'activity' => $data
            ]
        ];
    }
    /*
     * List sumary by category
     * 
     * Auth : 
     * Created : 22-03-2017
     */
    
    public function actionDetailSummary(){
        $request = Yii::$app->request;
        $param = $request->queryParams;
        $limit = isset($param['limit']) ? $param['limit'] : Yii::$app->params['limit']['sumary'];
        $offset = isset($param['offset']) ? $param['offset'] : Yii::$app->params['offset']['sumary'];
        $modelActivity = new ActivityApi();
        $modelActivity->scenario  = ActivityApi::SCENARIO_DETAIL_SUMMARY;
        $modelActivity->setAttributes($param);
        if (!$modelActivity->validate()) {
            return [
                    'status' => 400,
                    'messages' => $modelActivity->errors
                ];
        }
        
        $type = $modelActivity->type =  !empty($param['type']) ? $param['type'] : 1;
        $data = [];
        switch ($type) {
            case 1:
                $listComment = $modelActivity->getListCommnetByCategory($limit, $offset);
                if (count($listComment) > 0) {
                    $total = $modelActivity->getListCommnetByCategory($limit, $offset, true);
                    $offsetReturn = Utility::renderOffset($total, $limit, $offset);
                    $data['count'] = (int)$total;
                    $data['offset'] = $offsetReturn;
                    foreach ($listComment as $key => $value) {
                        $data['data_list'][] = [
                            'activity_id' => (int)$value['activity_id'],
                            'quiz_id' => (int)$value['quiz_id'],
                            'question' => $value['question'],
                            'content_comment' => $value['content'],
                            'member_id' => (int)Yii::$app->user->identity->member_id,
                            'member_name' => Yii::$app->user->identity->name,
                            'avatar' => Utility::getImage('member', Yii::$app->user->identity->member_id, null, true),
                            'created_date' => $value['created_date'],
                            'total_like' => (int)$value['total_like'],
                            'total_dis_like' => (int)$value['total_dis_like'],
                            'isDisLike' => Like::checkDisLikeByActivityId($value['activity_id'], Yii::$app->user->identity->member_id),
                            'isLike' => Like::checkLikeByActivityId($value['activity_id'], Yii::$app->user->identity->member_id)
                        ];
                    }
                }
                break;
            case 2:
                $listLike = $modelActivity->getListLikeByCategory($limit, $offset);
                if (count($listLike) > 0) {
                    $total = $modelActivity->getListLikeByCategory($limit, $offset, true);
                    $offsetReturn = Utility::renderOffset($total, $limit, $offset);
                    $data['count'] = (int)$total;
                    $data['offset'] = $offsetReturn;
                    foreach ($listLike as $key => $value) {
                        $data['data_list'][] = [
                            'activity_id' => (int)$value['activity_id'],
                            'quiz_id' => (int)$value['quiz_id'],
                            'content' => $value['content'],
                            'type' => (int)$value['type'],
                            'member_id' => (int)$value['member_id'],
                            'member_name' => $value['name'],
                            'avatar' => Utility::getImage('member', $value['member_id'], null, true),
                            'created_date' => $value['created_date'],
                            'total_like' => (int)$value['total_like'],
                            'total_dis_like' => (int)$value['total_dis_like'],
                            'isDisLike' => Like::checkDisLikeByActivityId($value['activity_id'], Yii::$app->user->identity->member_id),
                            'isLike' => Like::checkLikeByActivityId($value['activity_id'], Yii::$app->user->identity->member_id)
                        ];
                    }
                }
                break;
            case 3:
                $listNasi = $modelActivity->getListNasiByCategory($limit, $offset);
                if (count($listNasi) > 0) {
                    foreach ($listNasi as $key => $value) {
                        $infoActivity = ActivityApi::getInfoNasiByQuizId($value['quiz_id']);
                        if ($infoActivity) {
                            $data['data_list'][] = [
                                'activity_id' => (int)$infoActivity['activity_id'],
                                'quiz_id' => (int)$infoActivity['quiz_id'],
                                'content' => $infoActivity['content'],
                                'type' => (int)$infoActivity['type'],
                                'member_id' => (int)$infoActivity['member_id'],
                                'member_name' => $infoActivity['name'],
                                'avatar' => Utility::getImage('member', $infoActivity['member_id'], null, true),
                                'created_date' => $infoActivity['created_date'],
                                'total_like' => (int)$infoActivity['total_like'],
                                'total_dis_like' => (int)$infoActivity['total_dis_like'],
                                'isDisLike' => Like::checkDisLikeByActivityId($infoActivity['activity_id'], Yii::$app->user->identity->member_id),
                                'isLike' => Like::checkLikeByActivityId($infoActivity['activity_id'], Yii::$app->user->identity->member_id),
                            ];
                        }
                    }
                }
                break;
            default :
        }
        //return not found data
        if (count($data) == 0) {
            return [
                'status' => 204,
                'message' => \Yii::t('app', 'data not found')
            ];
        }
        //return data
        return [
            'status' => 200,
            'total_time_view' => (int)MemberCategoryTime::getTotalTimeViewByMainCategory($param['category_main_id']),
            'total_quiz' => (int)Quiz::getTotalQuizByCategory($param['category_main_id']),
            'total_ans_quiz' => (int)Quiz::getTotalQuizNasiByCategory($param['category_main_id']),
            'total_comment' => (int)Activity::getTotalQuizActivityByCategory($param['category_main_id'], Activity::TYPE_COMMENT),
            'total_like' => (int)Activity::getTotalQuizActivityByCategory($param['category_main_id'], Activity::TYPE_LIKE),
            'total_nasi' => (int)Quiz::getTotalQuizNasiByCategory($param['category_main_id']),
            'sub_menu' => Category::find()->select('name')->where(['parent_id' => $param['category_main_id']])->indexBy('cateory_id')->column(),
            'data' => $data
        ];
    }
    
    /*
     * Get rate activity Category
     * 
     * Auth : 
     * Created : 07-07-2017
     */
    public function getRateActivityByCategory($categoryId) {
        $totalActivityCommentHelpRelpy = Activity::getTotalQuizWithCommentHelpReplyByCat($categoryId);
        $totalActivityLike = Activity::getTotalQuizLikeByCat($categoryId);
        
        $totalActivity = $totalActivityCommentHelpRelpy + $totalActivityLike;
        $totalQuizByCategory = Quiz::find()->select('quiz_id')->where(['category_main_id' => $categoryId])->andWhere(['delete_flag' => Quiz::QUIZ_ACTIVE])->count();
        
        if ($totalQuizByCategory == 0) {
            return 0;
        }
        $rateActivity = round(100 * ($totalActivity / (2 * $totalQuizByCategory)), 2);
        return $rateActivity;
    }
}