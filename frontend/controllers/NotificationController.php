<?php
namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\QueryParamAuth;
use common\models\Notification;
use common\components\Utility;

/**
 * Site controller
 */
class NotificationController extends Controller
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
                    'list' => ['get']
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
     * function list notification
     * 
     * Auth : 
     * Create : 16-03-2016
     */
    
    public function actionList()
    {
        $request = Yii::$app->request;
        $param = $request->queryParams;
        $limit = isset($param['limit']) ? $param['limit'] : Yii::$app->params['limit']['notification'];
        $offset = isset($param['offset']) ? $param['offset'] : Yii::$app->params['offset']['notification'];
        $modelNotification = new Notification();
        $data = $modelNotification->renderListNotification($limit, $offset);
        //return no data
        if (count($data) == 0) {
            return [
                'status' => 204,
                'message' => \Yii::t('app', 'data not found')
            ];
        }
        //return data
        $total = Notification::getListNotificationByMember($limit, $offset, true);
        $offsetReturn = Utility::renderOffset($total, $limit, $offset);
        return [
            'status' => 200,
            'count' => (int)$total,
            'offset' => $offsetReturn,
            'data' => $data
            
        ];
    }
}