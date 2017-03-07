<?php
namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\QueryParamAuth;
use common\models\Member;
use common\models\Activity;
use common\components\Utility;
use frontend\models\LoginForm;
use frontend\models\FormUpload;
use yii\web\UploadedFile;
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
                    'avata' => ['put'],
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
        $memberDetail = Yii::$app->user->identity;
        return [
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
            return [
                    'status' => 400,
                    'messages' => $memberModel->errors
                ];
        }
        $memberModel->password = Yii::$app->security->generatePasswordHash($memberModel->password);
        $memberModel->auth_key = Yii::$app->security->generateRandomString(50);
        //return error
        if (!$memberModel->save()) {
            throw new \yii\base\Exception( "System error" );
            
        }
        //return success
        return [
            'status' => 200,
            'data' => [
                'access_token' => $memberModel->auth_key
            ]
        ];
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
        $memberModel = new Member();
        $memberModel->setAttributes($dataPost);
        $memberModel->mail = Yii::$app->user->identity->mail;
        //check param
        if (!$memberModel->validate()) {
            return [
                    'status' => 400,
                    'messages' => $memberModel->errors
                ];
        }
        //set validate
        $memberModelDetail = Member::findOne(['member_id' => Yii::$app->user->identity->member_id]);
        $memberModelDetail->setAttributes($dataPost);
        $memberModelDetail->password = Yii::$app->user->identity->password;
        if (!empty($dataPost['password'])) {
            $memberModelDetail->password = Yii::$app->security->generatePasswordHash($memberModel->password);
            $memberModelDetail->auth_key = Yii::$app->security->generateRandomString(50);
        }
        //return system error
        if (!$memberModelDetail->save()) {
            throw new \yii\base\Exception( "System error" );
            
        }
        //return success
        return [
            'status' => 200,
            'data' => [
                'access_token' => $memberModelDetail->auth_key
            ]
        ];
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
        //return error
        if (!$modelLogin->validate()) {
            return [
                'status' => 400,
                'messages' => $modelLogin->errors
            ];
        }
        //return success
        $member = Member::findOne(['mail' => $dataPost['mail']]);
        return [
            'status' => 200,
            'data' => [
                'access_token' => $member->auth_key
            ]
        ];
    }
    
    /*
     * Member avata
     * 
     * Auth :
     * Create : 25-02-2017
     */
    
    public function  actionAvata()
    {
//        $modelUpload = new FormUpload();
//        $request = Yii::$app->request;
//        $dataPost = $request->post();
        
        $putdata = fopen("php://input", "r");
           // make sure that you have /web/upload directory (writeable) 
           // for this to work
        $path = Yii::$app->params['imgPath'] . 'uploads'."/abc.png";

        $fp = fopen($path, "w");

        while ($data = fread($putdata, 1024))
           fwrite($fp, $data);

        /* Close the streams */
        fclose($fp);
        fclose($putdata);
        die('21212');
        
        
    }
}
