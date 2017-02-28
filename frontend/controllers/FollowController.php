<?php
namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\QueryParamAuth;


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
                    'add' => ['get'],
                    'delete' => ['get'],
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

    
    public function actionList()
    {
        return [
            'status' => 200,
            'data' => [
                [
                    'member_id' => 1,
                    'member_name' => 'anhct',
                    'info' => 'Ha Noi Dog sex and zend'
                ],
                [
                    'member_id' => 2,
                    'member_name' => 'hiennv',
                    'info' => 'Nghe an cat Co dau 8 tuoi'
                ],
            ]
        ];
    }
    
    public function actionFollowing()
    {
        return [
            'status' => 200,
            'data' => [
                [
                    'member_id' => 1,
                    'member_name' => 'anhct',
                    'info' => 'Ha Noi Dog sex and zend'
                ],
                [
                    'member_id' => 2,
                    'member_name' => 'hiennv',
                    'info' => 'Nghe an cat Co dau 8 tuoi'
                ],
            ]
        ];
    }
    
}
