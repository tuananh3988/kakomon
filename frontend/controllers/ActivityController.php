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
                    'deletecomment' => ['post'],
                    'listcomment' => ['get'],
                    'addHelp' => ['post'],
                    'deleteHelp' => ['post'],
                    'addReply' => ['post']
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
                'data' => [
                    'message' => \Yii::t('app', 'data not found')
                ]
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
    
    
    public function actionListComment()
    {
        //quiz_id
        return [
            'status' => 200,
            'count' => 100,//total comment by memberId
            'offset' => 10,
            'data' => [
                //list comment
                [
                    'member_id' => 123,
                    'member_name' => 'anhct',
                    'isDisLike' => true,
                    'isLike' => true,
                    'total_like' => 12,
                    'total_dislike' => 10
                ],
                [
                    'member_id' => 23,
                    'member_name' => 'hiennc',
                    'isDisLike' => true,
                    'isLike' => true,
                    'total_like' => 12,
                    'total_dislike' => 10
                ],
                [
                    'member_id' => 3,
                    'member_name' => 'thanhmc',
                    'isDisLike' => true,
                    'isLike' => false,
                    'total_like' => 1,
                    'total_dislike' => 2
                ]
            ],
            
        ];
    }
    
    public function actionListReply()
    {
        //ativity_id
        return [
            'status' => 200,
            'count' => 100,//count replly
            'offset' => 10,
            'data' => [
                [
                    'member_id' => 123,
                    'member_name' => 'anhct',
                    'isDisLike' => true,
                    'isLike' => true,
                    'total_like' => 12,
                    'total_dislike' => 10
                ],
                [
                    'member_id' => 23,
                    'member_name' => 'hiennc',
                    'isDisLike' => true,
                    'isLike' => true,
                    'total_like' => 12,
                    'total_dislike' => 10
                ],
                [
                    'member_id' => 3,
                    'member_name' => 'thanhmc',
                    'isDisLike' => true,
                    'isLike' => false,
                    'total_like' => 1,
                    'total_dislike' => 2
                ]
            ],
            
        ];
    }
    
    public function actionListHelp()
    {
        //quiz_id
        return [
            'status' => 200,
            'count' => 100,
            'offset' => 10,
            'data' => [
                [
                    'member_id' => 123,
                    'member_name' => 'anhct',
                    'isDisLike' => true,
                    'isLike' => true,
                    'total_like' => 12,
                    'total_dislike' => 10,
                    'reply' => [
                        [
                            'member_id' => 123,
                            'member_name' => 'anhct',
                            'isLike' => true,
                            'isDisLike' => true,
                            'total_like' => 12,
                            'total_dislike' => 10,
                        ],
                        [
                            'member_id' => 123,
                            'member_name' => 'anhct',
                            'isLike' => true,
                            'total_like' => 12,
                            'total_dislike' => 10,
                        ]
                    ],
                    'total_reply' => 30,
                    'offset_reply' => 10
                    
                ],
                [
                    'member_id' => 23,
                    'member_name' => 'hiennc',
                    'isLike' => true,
                    'total_like' => 12,
                    'total_dislike' => 10,
                    'reply' => [
                        [
                            'member_id' => 123,
                            'member_name' => 'anhct',
                            'isLike' => true,
                            'total_like' => 12,
                            'total_dislike' => 10,
                        ],
                        [
                            'member_id' => 123,
                            'member_name' => 'anhct',
                            'isLike' => true,
                            'total_like' => 12,
                            'total_dislike' => 10,
                        ]
                    ],
                    'total_reply' => 30,
                    'offset_reply' => 10
                ],
                [
                    'member_id' => 3,
                    'member_name' => 'thanhmc',
                    'isLike' => false,
                    'total_like' => 1,
                    'total_dislike' => 2,
                    'reply' => [
                        [
                            'member_id' => 123,
                            'member_name' => 'anhct',
                            'isLike' => true,
                            'total_like' => 12,
                            'total_dislike' => 10,
                        ],
                        [
                            'member_id' => 123,
                            'member_name' => 'anhct',
                            'isLike' => true,
                            'total_like' => 12,
                            'total_dislike' => 10,
                        ]
                    ],
                    'total_reply' => 30,
                    'offset_reply' => 10
                ]
            ],
            
        ];
    }
    /*
     * List comment
     * 
     * Auth : 
     * Create : 01-03-2017
     */
    
//    public function actionListcomment()
//    {
//        $request = Yii::$app->request;
//        $param = $request->queryParams;
//        $limit = isset($param['limit']) ? $param['limit'] : Yii::$app->params['limit'];
//        $offset = isset($param['offset']) ? $param['offset'] : Yii::$app->params['offset'];
//        $memberDetail = Member::findOne(['auth_key' => $param['access-token']]);
//        
//        $modelActivity = new Activity();
//        $listComment = $modelActivity->getListComment($memberDetail->member_id, $limit, $offset);
//        if (count($listComment) == 0) {
//            return [
//                    'status' => 204,
//                    'data' => [
//                        'message' => \Yii::t('app', 'data not found')
//                    ]
//                ];
//        }
//        // show list commnet
//        foreach ($listComment as $key => $value) {
//            
//        }
//        
//        
//    }
    
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
                'data' => [
                    'message' => \Yii::t('app', 'data not found')
                ]
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
        $activityDetail = Activity::findOne(['activity_id' => $modelReply->activity_id, 'member_id' => Yii::$app->user->identity->member_id]);
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
}