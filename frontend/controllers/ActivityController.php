<?php
namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\QueryParamAuth;
use common\components\Utility;
use common\models\Member;
use common\models\Activity;
use common\models\Category;
use common\models\Quiz;
use frontend\models\Like;
use frontend\models\Comment;
use frontend\models\Help;
use frontend\models\Reply;

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
                    'deleteComment' => ['post'],
                    'listComment' => ['get'],
                    'addHelp' => ['post'],
                    'deleteHelp' => ['post'],
                    'listHelp' => ['get'],
                    'addReply' => ['post'],
                    'deleteReply' => ['post'],
                    'listReply' => ['get'],
                    'timeline' => ['get']
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
            $modelActivitySave = new Activity();
            $modelActivitySave->member_id = Yii::$app->user->identity->member_id;
            $modelActivitySave->status = $modelLike->status;
            $modelActivitySave->type = Activity::TYPE_LIKE;
            $modelActivitySave->quiz_id = $activityDetail->quiz_id;
            $modelActivitySave->relate_id = $modelLike->activity_id;
            if (!$modelActivitySave->save()) {
                throw new \yii\base\Exception( "System error" );
            }
            //update status for record like
            if ($activityDetailDisLike) {
                $activityDetailDisLike->status = Activity::STATUS_DELETE;
                $activityDetailDisLike->save();
            }
            return  [
                    'status' => 200,
                    'data' => [
                        'activity_id' => $modelActivitySave->activity_id
                    ]
                ];
        } else {
            //update status if status post diff status old
            if ($activityDetailOld->status != $modelLike->status) {
                $activityDetailOld->status = $modelLike->status;
                if (!$activityDetailOld->save()) {
                    throw new \yii\base\Exception( "System error" );
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
            $modelActivitySave = new Activity();
            $modelActivitySave->member_id = Yii::$app->user->identity->member_id;
            $modelActivitySave->status = $modelLike->status;
            $modelActivitySave->type = Activity::TYPE_DISLIKE;
            $modelActivitySave->quiz_id = $activityDetail->quiz_id;
            $modelActivitySave->relate_id = $modelLike->activity_id;
            if (!$modelActivitySave->save()) {
                throw new \yii\base\Exception( "System error" );
            }
            //update status for record like
            if ($activityDetailLike) {
                $activityDetailLike->status = Activity::STATUS_DELETE;
                $activityDetailLike->save();
            }
            return  [
                    'status' => 200,
                    'data' => [
                        'activity_id' => $modelActivitySave->activity_id
                    ]
                ];
        } else {
            //update status if status post diff status old
            if ($activityDetailOld->status != $modelLike->status) {
                $activityDetailOld->status = $modelLike->status;
                if (!$activityDetailOld->save()) {
                    throw new \yii\base\Exception( "System error" );
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
        
        return  [
            'status' => 200,
            'data' => [
                'activity_id' => $modelComment->activity_id
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
        $commentDetail->status = Activity::STATUS_DELETE;
        //return error system
        if (!$commentDetail->save()) {
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
        
        //return data
        $total = Help::getTotalHelpByQuizId($param['quiz_id']);
        $offsetReturn = Utility::renderOffset($total, $limit, $offset);
        return [
            'status' => 200,
            'count' => (int)$total,
            'offset' => $offsetReturn,
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
        //return if not found data
        if (!$helpDetail) {
            return [
                'status' => 204,
                'message' => \Yii::t('app', 'data not found')
            ];
        }
        
        $helpDetail->status = Activity::STATUS_DELETE;
        //return system error
        if (!$helpDetail->save()) {
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
        //save data
        $dataSave = new Reply();
        $dataSave->member_id = Yii::$app->user->identity->member_id;
        $dataSave->status = Activity::STATUS_ACTIVE;
        $dataSave->type = Activity::TYPE_REPLY;
        $dataSave->relate_id = $activityDetail->activity_id;
        $dataSave->quiz_id = $activityDetail->quiz_id;
        $dataSave->content = $modelReply->content;
        //return system error
        if (!$dataSave->save()) {
            throw new \yii\base\Exception( "System error" );
        }
        //return success
        return  [
            'status' => 200,
            'data' => [
                'activity_id' => $dataSave->activity_id
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
        //return if not found data
        if (!$replyDetail) {
            return [
                'status' => 204,
                'message' => \Yii::t('app', 'data not found')
            ];
        }
        
        $replyDetail->status = Activity::STATUS_DELETE;
        //return system error
        if (!$replyDetail->save()) {
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
        $categoryFirst = Category::find()->where(['parent_id' => 0])->orderBy(['cateory_id' => SORT_ASC])->one();
        if (!isset($param['category_id']) && !$categoryFirst) {
            return [
                'status' => 204,
                'message' => \Yii::t('app', 'data not found')
            ];
        }
        $categoryId = isset($param['category_id']) ? $param['category_id'] : $categoryFirst->cateory_id;
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
                'content_activity' => $value['content_activity'],
                'question' => $value['question'],
                'sub_menu' => Quiz::renderListSubCat($value['category_a_id'], $value['category_b_id']),
                'cateory_id' => (int)$value['cateory_id'],
                'member_id' => $value['member_id'],
                'name' => $value['name'],
                'avatar' => ''
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
}