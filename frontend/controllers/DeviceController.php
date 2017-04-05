<?php
namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\QueryParamAuth;
use frontend\models\MemberDevicesApi;

/**
 * Site controller
 */
class DeviceController extends Controller
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
                    'delete' => ['post']
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
     * Action delete
     * 
     * Auth : 
     * Create : 01-03-2017
     */
    
    public function actionDelete()
    {
        $request = Yii::$app->request;
        $dataPost = $request->post();
        $modelMemberDevices = new MemberDevicesApi();
        $modelMemberDevices->setAttributes($dataPost);
        if (!$modelMemberDevices->validate()) {
            return [
                    'status' => 400,
                    'messages' => $modelMemberDevices->errors
                ];
        }
        $memberDevice = MemberDevicesApi::findOne(['member_id' => Yii::$app->user->identity->member_id, 'device_id' => $modelMemberDevices->device_id]);
        $memberDevice->delete_flag = \common\models\MemberDevices::DEVICE_DELETED;
        if (!$memberDevice->save()) {
            throw new \yii\base\Exception( "System error" );
            
        }
        //return success
        return [
            'status' => 200
        ];
    }
}