<?php
namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\QueryParamAuth;
use common\models\Follow;
use common\models\Member;

/**
 * Site controller
 */
class FollowController extends Controller
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
                    'list' => ['get'],
                    'following' => ['get'],
                    'add' => ['post'],
                    'delete' => ['post'],
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
     * List follow
     * 
     * Auth : 
     * Create : 28-02-2017
     */
    
    public function actionList()
    {
        $request = Yii::$app->request;
        $param = $request->queryParams;
        $limit = isset($param['limit']) ? $param['limit'] : Yii::$app->params['limit'];
        $offset = isset($param['offset']) ? $param['offset'] : Yii::$app->params['offset'];
        
        $memberDetail = Member::findOne(['auth_key' => $param['access-token']]);
        $modelFollow = new Follow();
        $listFollow = $modelFollow->getListFollow($memberDetail->member_id, $limit, $offset);
        
        if (count($listFollow) > 0) {
            $listData = [];
            foreach ($listFollow as $key => $value) {
                $listData[] = [
                    'member_id' => $value['member_id'],
                    'member_name' => $value['name'],
                    'info' => $value['city'] . ' ' . $value['favorite_animal'] . ' ' . $value['favorite_film']
                ];
            }
            $result =[
                'status' => 200,
                'data' => $listData
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
     * List following
     * 
     * Auth : 
     * Create : 28-02-2017
     */
    
    
    public function actionFollowing()
    {
        $request = Yii::$app->request;
        $param = $request->queryParams;
        $limit = isset($param['limit']) ? $param['limit'] : Yii::$app->params['limit'];
        $offset = isset($param['offset']) ? $param['offset'] : Yii::$app->params['offset'];
        
        $memberDetail = Member::findOne(['auth_key' => $param['access-token']]);
        $modelFollow = new Follow();
        $listFollow = $modelFollow->getListFollowing($memberDetail->member_id, $limit, $offset);
        
        if (count($listFollow) > 0) {
            $listData = [];
            foreach ($listFollow as $key => $value) {
                $listData[] = [
                    'member_id' => $value['member_id'],
                    'member_name' => $value['name'],
                    'info' => $value['city'] . ' ' . $value['favorite_animal'] . ' ' . $value['favorite_film']
                ];
            }
            $result =[
                'status' => 200,
                'data' => $listData
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
     * Add follow
     * 
     * Auth : 
     * Create : 28-02-2017
     */
    
    public function actionAdd()
    {
        $modelFollow = new Follow();
        $request = Yii::$app->request;
        $dataPost = $request->post();
        $param = $request->queryParams;
        $memberModel = Member::findOne(['auth_key' => $param['access-token']]);
        
        //set value
        $modelFollow->member_id_followed = $memberModel->member_id;
        $modelFollow->member_id_following = isset($dataPost['member_id']) ? $dataPost['member_id'] : '';
        $modelFollow->scenario  = Follow::SCENARIO_FOLLOW;
        //validate param
        if (!$modelFollow->validate()) {
            $result = [
                'status' => 400,
                'messages' => $modelFollow->errors
            ];
        } else {
            if ($modelFollow->save()) {
                $result = [
                    'status' => 200
                ];
            } else {
                throw new \yii\base\Exception( "System error" );
            }
        }
        
        return $result;
    }
    
    /*
     * Delete follow
     * 
     * Auth : 
     * Create : 28-02-2017
     */
    
    public function actionDelete()
    {
        $modelFollow = new Follow();
        $request = Yii::$app->request;
        $dataPost = $request->post();
        $param = $request->queryParams;
        $memberModel = Member::findOne(['auth_key' => $param['access-token']]);
        
        //set value
        $modelFollow->member_id_followed = $memberModel->member_id;
        $modelFollow->member_id_following = isset($dataPost['member_id']) ? $dataPost['member_id'] : '';
        //validate param
        if (!$modelFollow->validate()) {
            $result = [
                'status' => 400,
                'messages' => $modelFollow->errors
            ];
        } else {
            $followDetail = Follow::findOne(['member_id_followed' => $memberModel->member_id, 'member_id_following' => $dataPost['member_id'], 'delete_flag' => 0]);
            if ($followDetail) {
                $followDetail->delete_flag = 1;
                if ($followDetail->save()) {
                    $result = [
                        'status' => 200
                    ];
                } else {
                    throw new \yii\base\Exception( "System error" );
                }
            } else {
                $result = [
                    'status' => 204,
                    'data' => [
                        'message' => \Yii::t('app', 'data not found')
                    ]
                ];
            }
        }
        
        return $result;
    }
}
