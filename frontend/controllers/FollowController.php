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
        $limit = isset($param['limit']) ? $param['limit'] : Yii::$app->params['limit']['follow'];
        $offset = isset($param['offset']) ? $param['offset'] : Yii::$app->params['offset']['follow'];
        
        $modelFollow = new Follow();
        $listFollow = $modelFollow->getListFollow(Yii::$app->user->identity->member_id, $limit, $offset);
        
        if (count($listFollow) == 0) {
            return [
                'status' => 204,
                'message' => \Yii::t('app', 'data not found')
            ];
        }
        $listData = [];
        foreach ($listFollow as $key => $value) {
            $listData[] = [
                'member_id' => $value['member_id'],
                'member_name' => $value['name'],
                'info' => $value['city'] . ' ' . $value['favorite_animal'] . ' ' . $value['favorite_film']
            ];
        }
        return [
            'status' => 200,
            'data' => $listData
        ];
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
        $limit = isset($param['limit']) ? $param['limit'] : Yii::$app->params['limit']['follow'];
        $offset = isset($param['offset']) ? $param['offset'] : Yii::$app->params['offset']['follow'];
        
        $modelFollow = new Follow();
        $listFollow = $modelFollow->getListFollowing(Yii::$app->user->identity->member_id, $limit, $offset);
        //return if not found data
        if (count($listFollow) ==  0) {
            return [
                'status' => 204,
                'message' => \Yii::t('app', 'data not found')
            ];
            
        }
        //return data
        $listData = [];
        foreach ($listFollow as $key => $value) {
            $listData[] = [
                'member_id' => $value['member_id'],
                'member_name' => $value['name'],
                'info' => $value['city'] . ' ' . $value['favorite_animal'] . ' ' . $value['favorite_film']
            ];
        }
        return [
            'status' => 200,
            'data' => $listData
        ];
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
        //set value
        $modelFollow->member_id_followed = Yii::$app->user->identity->member_id;
        $modelFollow->member_id_following = isset($dataPost['member_id']) ? $dataPost['member_id'] : '';
        $modelFollow->scenario  = Follow::SCENARIO_FOLLOW;
        //validate param
        if (!$modelFollow->validate()) {
            return [
                'status' => 400,
                'messages' => $modelFollow->errors
            ];
        }
        //return after save data
        if ($modelFollow->save()) {
            return [
                'status' => 200
            ];
        } else {
            throw new \yii\base\Exception( "System error" );
        }
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
        
        //set value
        $modelFollow->member_id_followed = Yii::$app->user->identity->member_id;
        $modelFollow->member_id_following = isset($dataPost['member_id']) ? $dataPost['member_id'] : '';
        //validate param
        if (!$modelFollow->validate()) {
            return [
                'status' => 400,
                'messages' => $modelFollow->errors
            ];
        }
        $followDetail = Follow::findOne(['member_id_followed' => Yii::$app->user->identity->member_id, 'member_id_following' => $dataPost['member_id'], 'delete_flag' => 0]);
        //retunr if not found data
        if (!$followDetail) {
            return [
                'status' => 204,
                'message' => \Yii::t('app', 'data not found')
            ];
            
        }
        //return after save
        $followDetail->delete_flag = 1;
        if (!$followDetail->save()) {
            throw new \yii\base\Exception( "System error" );
        }
        return [
                'status' => 200
            ];
    }
}
