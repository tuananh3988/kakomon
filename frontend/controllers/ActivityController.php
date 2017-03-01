<?php
namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\QueryParamAuth;
use common\models\Member;
use common\models\Activity;
use frontend\models\Like;
use frontend\models\Comment;

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
                    'add' => ['post'],
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
        $param = $request->queryParams;
        $memberDetail = Member::findOne(['auth_key' => $param['access-token']]);
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
        $activityDetailOld = Activity::findOne(['member_id' => $memberDetail->member_id, 'type' => Activity::TYPE_LIKE, 'status' => 1, 'relate_id' => $modelLike->activity_id]);
        if (!$activityDetailOld) {
            $modelActivitySave = new Activity();
            $modelActivitySave->member_id = $memberDetail->member_id;
            $modelActivitySave->status = $modelLike->status;
            $modelActivitySave->type = Activity::TYPE_LIKE;
            $modelActivitySave->quiz_id = $activityDetail->quiz_id;
            $modelActivitySave->relate_id = $modelLike->activity_id;
            if ($modelActivitySave->save()) {
                return  [
                        'status' => 200
                    ];
            } else {
                throw new \yii\base\Exception( "System error" );
            }
        } else {
            $activityDetailOld->status = $modelLike->status;
            if ($activityDetailOld->save()) {
                return  [
                        'status' => 200
                    ];
            } else {
                throw new \yii\base\Exception( "System error" );
            }
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
        $param = $request->queryParams;
        $memberDetail = Member::findOne(['auth_key' => $param['access-token']]);
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
        $activityDetailOld = Activity::findOne(['member_id' => $memberDetail->member_id, 'type' => Activity::TYPE_DISLIKE, 'status' => 1, 'relate_id' => $modelLike->activity_id]);
        if (!$activityDetailOld) {
            $modelActivitySave = new Activity();
            $modelActivitySave->member_id = $memberDetail->member_id;
            $modelActivitySave->status = $modelLike->status;
            $modelActivitySave->type = Activity::TYPE_DISLIKE;
            $modelActivitySave->quiz_id = $activityDetail->quiz_id;
            $modelActivitySave->relate_id = $modelLike->activity_id;
            if ($modelActivitySave->save()) {
                return  [
                        'status' => 200
                    ];
            } else {
                throw new \yii\base\Exception( "System error" );
            }
        } else {
            $activityDetailOld->status = $modelLike->status;
            if ($activityDetailOld->save()) {
                return  [
                        'status' => 200
                    ];
            } else {
                throw new \yii\base\Exception( "System error" );
            }
        }
    }
    
    
    /*
     * Add comment
     * 
     * Auth : 
     * Create : 01-03-2017
     */
    
    public function actionAdd()
    {
        $request = Yii::$app->request;
        $param = $request->queryParams;
        $memberDetail = Member::findOne(['auth_key' => $param['access-token']]);
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
        $modelActivitySave = new Activity();
        $modelActivitySave->member_id = $memberDetail->member_id;
        $modelActivitySave->status = Activity::STATUS_ACTIVE;
        $modelActivitySave->type = Activity::TYPE_COMMENT;
        $modelActivitySave->quiz_id = $modelComment->quiz_id;
        $modelActivitySave->content = $modelComment->content;
        if ($modelActivitySave->save()) {
            return  [
                    'status' => 200,
                    'data' => [
                        'activity_id' => $modelActivitySave->activity_id
                    ]
                ];
        } else {
            throw new \yii\base\Exception( "System error" );
        }
    }
}