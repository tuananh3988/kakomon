<?php
namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\QueryParamAuth;
use common\models\Member;
use common\models\Activity;
use common\models\Quiz;
use common\models\Follow;
use common\components\Utility;
use frontend\models\LoginForm;
use frontend\models\FormUpload;
use frontend\models\MemberApi;
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
                    'avatar' => ['post'],
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
                'avatar' => Utility::getImage('member', $memberDetail->member_id, null, true)
            ]
        ];
    }
    
    /*
     * my info
     * 
     * Auth :
     * Create : 27-02-2017
     */
    
    public function actionMyInfo()
    {
        $memberDetail = Yii::$app->user->identity;
        return [
            'status' => 200,
            'data' => [
                'id' => (int)$memberDetail->member_id,
                'name' => $memberDetail->name,
                'city' => $memberDetail->city,
                'job' => $memberDetail->job,
                'type_blood' => $memberDetail->type_blood,
                'favorite_animal' => $memberDetail->favorite_animal,
                'favorite_film' => $memberDetail->favorite_film,
                'birthday' => $memberDetail->birthday,
                'sex' => $memberDetail->sex,
                'furigana' => $memberDetail->furigana,
                'mail' => $memberDetail->mail,
                'avatar' => Utility::getImage('member', $memberDetail->member_id, null, true),
                'comment' => (int)Activity::getTotalCommentByMember($memberDetail->member_id),
                'liked' => (int)Activity::getTotalLikeByMember($memberDetail->member_id),
                'disLike' => (int)Activity::getTotalDisLikeByMember($memberDetail->member_id),
                'nashi' => (int)Quiz::getTotalQuizNotAnsByCategory(),
                'followed' => (int)Follow::getTotalFollowedByMember($memberDetail->member_id),
                'following' => (int)Follow::getTotalFollowingByMember($memberDetail->member_id),
            ]
        ];
    }
    
    /*
     * info
     * 
     * Auth :
     * Create : 26-03-2017
     */
    
    public function actionInfo()
    {
        $request = Yii::$app->request;
        $param = $request->queryParams;
        $limit = isset($param['limit']) ? $param['limit'] : Yii::$app->params['limit']['sumary'];
        $offset = isset($param['offset']) ? $param['offset'] : Yii::$app->params['offset']['sumary'];
        
        $modelMember = new MemberApi();
        $modelMember->setAttributes($param);
        $modelMember->scenario  = MemberApi::SCENARIO_INFO;
        if (!$modelMember->validate()) {
            return [
                'status' => 400,
                'messages' => $modelMember->errors
            ];
        }
        $memberDetail = Member::findOne(['member_id' => $modelMember->member_id]);
        return [
            'status' => 200,
            'data' => [
                'id' => (int)$memberDetail->member_id,
                'name' => $memberDetail->name,
                'city' => $memberDetail->city,
                'job' => $memberDetail->job,
                'type_blood' => $memberDetail->type_blood,
                'favorite_animal' => $memberDetail->favorite_animal,
                'favorite_film' => $memberDetail->favorite_film,
                'birthday' => $memberDetail->birthday,
                'sex' => $memberDetail->sex,
                'furigana' => $memberDetail->furigana,
                'mail' => $memberDetail->mail,
                'isFollow' => Follow::checkFollowing($memberDetail->member_id),
                'avatar' => Utility::getImage('member', $memberDetail->member_id, null, true),
                'comment' => (int)Activity::getTotalCommentByMember($memberDetail->member_id),
                'liked' => (int)Activity::getTotalLikeByMember($memberDetail->member_id),
                'disLike' => (int)Activity::getTotalDisLikeByMember($memberDetail->member_id),
                'nashi' => (int)Quiz::getTotalQuizNotAnsByCategory(),
                'followed' => (int)Follow::getTotalFollowedByMember($memberDetail->member_id),
                'following' => (int)Follow::getTotalFollowingByMember($memberDetail->member_id),
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
    
    public function  actionAvatar()
    {
        $modelUpload = new FormUpload();
        $modelUpload->file = $_FILES;
        //return error
        if (!$modelUpload->validate()) {
            return [
                'status' => 400,
                'messages' => $modelUpload->errors
            ];
        }
        $utility = new Utility();
        $utility->uploadImagesForApi($modelUpload->file, 'member', Yii::$app->user->identity->member_id);
        return [
            'status' => 200,
            'data' => [
                'avatar' => Utility::getImage('member', Yii::$app->user->identity->member_id, null, true)
            ]
        ];
    }
}
