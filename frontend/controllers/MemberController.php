<?php
namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\QueryParamAuth;
use common\models\Member;
use common\models\Activity;
use frontend\models\LoginForm;
/**
 * Site controller
 */
class MemberController extends Controller
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
                    'detail' => ['get'],
                    'my-info' => ['get'],
                    'info' => ['get'],
                    'create' => ['post'],
                    'login' => ['post'],
                    'update' => ['post'],
                ],
            ],
            'authenticator' => [
                'class' => CompositeAuth::className(),
                'except' => ['create', 'login'],
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
     *Member detail
     * 
     * Auth :
     * Create : 25-02-2017
     */
    
    public function actionDetail()
    {
        $request = Yii::$app->request;
        $param = $request->queryParams;
        $result = [];
        $memberDetail = Member::findOne(['auth_key' => $param['access-token']]);
        if ($memberDetail) {
            $result = [
                'status' => 200,
                'data' => [
                    'member_id' => $memberDetail->member_id,
                    'city' => $memberDetail->city,
                    'job' => $memberDetail->job,
                    'type_blood' => $memberDetail->type_blood,
                    'favorite_animal' => $memberDetail->favorite_animal,
                    'favorite_film' => $memberDetail->favorite_film,
                    'birthday' => $memberDetail->birthday,
                    'sex' => $memberDetail->sex,
                    'name' => $memberDetail->name,
                    'furigana' => $memberDetail->furigana,
                    'mail' => $memberDetail->mail,
                    'nickname' => $memberDetail->nickname,
                    'link_avatar' => ''
                ]
            ];
        } else {
            $result = [
                'status' => 204,
                'data' => [
                    'message' => \Yii::t('app', 'data not found')
                ]
            ];
        }
        
        return $result;
    }
    
    /*
     *Member detail
     * 
     * Auth :
     * Create : 27-02-2017
     */
    
    public function actionMyInfo()
    {
        $request = Yii::$app->request;
        $param = $request->queryParams;
        $result = [];
        $memberDetail = Member::findOne(['auth_key' => $param['access-token']]);
        if ($memberDetail) {
            $result = [
                'status' => 200,
                'data' => [
                    'id' => $memberDetail->member_id,
                    'name' => $memberDetail->name,
                    'comment' => Activity::getTotalCommentByMember($memberDetail->member_id),
                    'liked' => Activity::getTotalLikeByMember($memberDetail->member_id)
                ]
            ];
        } else {
            $result = [
                'status' => 204,
                'data' => [
                    'message' => \Yii::t('app', 'data not found')
                ]
            ];
        }
        
        return $result;
        
        return [
            'status' => 200,
                'data' => [
                    'id' => 1,
                    'name' => 'anhct',
                    'comment' => 101,
                    'liked' => 900,
                    'be_liked' => 810,
                    'nashi' => 1500,
                    'followed' => 200,
                    'following' => 99,
                    'category_activity' => [
                        [
                            'category_id' => 1,
                            'category_name' => 'sex',
                            'total_quiz' => 600,
                            'time_view' => 2,
                            'total_complete' => 700,
                            'comment' => 100,
                            'like' => 200,
                            'nashi' => 300,
                            
                        ],
                        [
                            'category_id' => 2,
                            'category_name' => 'make love',
                            'total_quiz' => 600,
                            'time_view' => 2,
                            'total_complete' => 700,
                            'comment' => 100,
                            'like' => 200,
                            'nashi' => 300,
                            
                        ]
                    ]
                ]
        ];
    }
    
    public function actionInfo($id)
    {
        return [
            'status' => 200,
            'data' => [
                'id' => 2,
                'name' => 'hiennv',
                'followed' => 200,
                'following' => 99,
                'activity' => [
                    [
                        'category_id' => 1,
                        'category_name' => 'sex',
                        'sub_category_id' => '12',
                        'sub_category_name' => 'sex',
                        'type' => 1,
                        'like' => 100,
                        'dislike' => 200,
                        'member_name' => 'anhct',
                        'comment' => 'abc'
                    ],
                    [
                        'category_id' => 2,
                        'category_name' => 'sex',
                        'sub_category_id' => '12',
                        'sub_category_name' => 'sex',
                        'type' => 2,
                        'like' => 100,
                        'dislike' => 200,
                        'member_name' => 'anhct',
                        'comment' => 'abc'
                    ],
                    [
                        'category_id' => 3,
                        'category_name' => 'sex',
                        'sub_category_id' => '12',
                        'sub_category_name' => 'sex',
                        'type' => 3,
                        'like' => 100,
                        'dislike' => 200,
                        'member_name' => 'anhct',
                        'comment' => 'abc'
                    ]
                    
                ]
            ]
        ];
    }
    
    /*
     * Create member
     * 
     * Auth :
     * Create : 25-02-2017
     */

    public function actionCreate()
    {
        $request = Yii::$app->request;
        $dataPost = $request->post();
        $memberModel = new Member();
        $memberModel->setAttributes($dataPost);
        $memberModel->scenario  = Member::SCENARIO_SAVE;
        //validate param
        if (!$memberModel->validate()) {
            $result = [
                'status' => 400,
                'messages' => $memberModel->errors
            ];
        } else {
            $memberModel->password = Yii::$app->security->generatePasswordHash($memberModel->password);
            $memberModel->auth_key = Yii::$app->security->generateRandomString(50);
            if ($memberModel->save()) {
                $result = [
                    'status' => 200,
                    'data' => [
                        'access_token' => $memberModel->auth_key
                    ]
                ];
            } else {
                throw new \yii\base\Exception( "System error" );
            }
        }
        
        return $result;
    }

    
    /*
     * Update member
     * 
     * Auth :
     * Create : 25-02-2017
     */
    
    public function actionUpdate()
    {
        $request = Yii::$app->request;
        $dataPost = $request->post();
        $param = $request->queryParams;
        $memberModel = Member::findOne(['auth_key' => $param['access-token']]);
        $oldPassWord = $memberModel->password;
        $memberModel->setAttributes($dataPost);
        //set old password
        if (!$memberModel->password) {
            $memberModel->password = $oldPassWord;
        } else {
            $memberModel->password = Yii::$app->security->generatePasswordHash($memberModel->password);
            $memberModel->auth_key = Yii::$app->security->generateRandomString(50);
        }
        //check param
        if (!$memberModel->validate()) {
            $result = [
                'status' => 400,
                'messages' => $memberModel->errors
            ];
        } else {
            if ($memberModel->save()) {
                $result = [
                    'status' => 200,
                    'data' => [
                        'access_token' => $memberModel->auth_key
                    ]
                ];
            } else {
                throw new \yii\base\Exception( "System error" );
            }
        }
        
        return $result;
    }
    
    /*
     * Member Login
     * 
     * Auth :
     * Create : 25-02-2017
     */
    
    public function  actionLogin()
    {
        $modelLogin = new LoginForm();
        $request = Yii::$app->request;
        $dataPost = $request->post();
        $modelLogin->setAttributes($dataPost);
        if (!$modelLogin->validate()) {
            $result = [
                'status' => 400,
                'messages' => $modelLogin->errors
            ];
        } else {
            $member = Member::findOne(['mail' => $dataPost['mail']]);
            $result = [
                'status' => 200,
                'data' => [
                    'access_token' => $member->auth_key
                ]
            ];
        }
        
        return $result;
    }
    
}
