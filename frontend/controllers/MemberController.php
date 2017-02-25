<?php
namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\QueryParamAuth;
use common\models\Member;
use yii\web\Response;

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
                    'nickname' => $memberDetail->nickname,
                ]
            ];
        } else {
            $result = [
                'status' => 200,
                'data' => [
                    'message' => \Yii::t('app', 'data not found')
                ]
            ];
        }
        
        return $result;
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
        $reason = [];
        //validate param
        if (!isset($dataPost['birthday']) || ($dataPost['birthday'] == '')) {
            $reason['birthday'] = \Yii::t('app', 'required') . ' birthday';
        }
        if (isset($dataPost['birthday']) && (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$dataPost['birthday']) == 0)) {
            
            $reason['birthday'] = 'Birthday '. \Yii::t('app', 'format');
        }
        
        if (!isset($dataPost['sex']) || ($dataPost['sex'] == '')) {
            $reason['sex'] = \Yii::t('app', 'required') . ' sex';
        }
        
        if (isset($dataPost['sex']) && (!is_numeric($dataPost['sex']))) {
            $reason['sex'] = 'Sex '. \Yii::t('app', 'format');
        }
        
        if (!isset($dataPost['mail']) || ($dataPost['mail'] == '')) {
            $reason['mail'] = \Yii::t('app', 'required') . ' mail';
        }
        
        if (isset($dataPost['mail']) && Member::findEmail($dataPost['mail'])) {
            $reason['mail'] = \Yii::t('app', 'field exists', ['field' => 'mail']);
        }
        
        $pattern = "^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$";
        
        if (isset($dataPost['mail']) && (!eregi($pattern,$dataPost['mail']))) {
            $reason['mail'] = 'Mail '. \Yii::t('app', 'format');
        }
        
        if (!isset($dataPost['password']) || ($dataPost['password'] == '')) {
            $reason['password'] = \Yii::t('app', 'required') . ' password';
        }
        
        if (isset($dataPost['password']) && (strlen($dataPost['password']) < 8)) {
            $reason['password'] = \Yii::t('app', 'min length', ['field' => 'password', 'min_length' => 8]);
        }
        if (!empty($reason)) {
            $result = [
                'status' => 200,
                'result' => 0,
                'data' => $reason
            ];
        } else {
            $memberModel = new Member();
            $memberModel->setAttributes($dataPost);
            $memberModel->password = Yii::$app->security->generatePasswordHash($memberModel->password);
            $memberModel->auth_key = Yii::$app->security->generateRandomString(50);
            if ($memberModel->save()) {
                $result = [
                    'status' => 200,
                    'result' => 1,
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
        $reason = [];
        //validate param
        if (!isset($dataPost['birthday']) || ($dataPost['birthday'] == '')) {
            $reason['birthday'] = \Yii::t('app', 'required') . ' birthday';
        }
        if (isset($dataPost['birthday']) && (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$dataPost['birthday']) == 0)) {
            
            $reason['birthday'] = 'Birthday '. \Yii::t('app', 'format');
        }
        
        if (!isset($dataPost['sex']) || ($dataPost['sex'] == '')) {
            $reason['sex'] = \Yii::t('app', 'required') . ' sex';
        }
        
        if (isset($dataPost['sex']) && (!is_numeric($dataPost['sex']))) {
            $reason['sex'] = 'Sex '. \Yii::t('app', 'format');
        }
        
        if (!empty($reason)) {
            $result = [
                'status' => 200,
                'result' => 0,
                'data' => $reason
            ];
        } else {
            $memberModel = Member::findOne(['auth_key' => $param['access-token']]);
            $memberModel->setAttributes($dataPost);
            $memberModel->auth_key = Yii::$app->security->generateRandomString(50);
            if ($memberModel->save()) {
                $result = [
                    'status' => 200,
                    'result' => 1,
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
        $request = Yii::$app->request;
        $dataPost = $request->post();
        $reason = [];
        //validate param
        if (!isset($dataPost['mail']) || ($dataPost['mail'] == '')) {
            $reason['mail'] = \Yii::t('app', 'required') . ' mail';
        }
        
        $pattern = "^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$";
        
        if (isset($dataPost['mail']) && (!eregi($pattern,$dataPost['mail']))) {
            $reason['mail'] = 'Mail '. \Yii::t('app', 'format');
        }
        
        if (!isset($dataPost['password']) || ($dataPost['password'] == '')) {
            $reason['password'] = \Yii::t('app', 'required') . ' password';
        }
        
        if (isset($dataPost['mail']) && isset($dataPost['password'])) {
            $memberDetail = Member::findOne(['mail' => $dataPost['mail']]);
            if (!$memberDetail) {
                $reason['mail'] = 'Incorrect email address';
            } else {
                if (!Yii::$app->security->validatePassword($dataPost['password'],$memberDetail->password)) {
                    $reason['password'] = 'Incorrect password';
                }
            }
        }
        if (!empty($reason)) {
            $result = [
                'status' => 200,
                'result' => 0,
                'data' => $reason
            ];
        } else {
            $result = [
                'status' => 200,
                'result' => 1,
                'data' => [
                    'access_token' => $memberDetail->auth_key
                ]
            ];
        }
        
        return $result;
    }
    
}
