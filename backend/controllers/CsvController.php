<?php

namespace backend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\web\Session;
use common\models\LogCsv;

class CsvController extends Controller {
    
    public function behaviors() {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['index', 'detail'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                //'logout' => ['post'],
                ],
            ],
        ];
    }
    
    /**
     * Index csv
     *
     * @date : 15-02-2017
     *
     */
    
    public function actionIndex() {
        $formSearch = new LogCsv();
        $dataProvider = $formSearch->getData();
        return $this->render('index', [
            'dataProvider' => $dataProvider
        ]);
    }
    
    /**
     * detail csv
     *
     * @date : 15-02-2017
     *
     */
    
    public function actionDetail($logId) {
        $model = new LogCsv();
        $logCsvItem = $model->find()->where(['log_id' => $logId, 'status' => LogCsv::STATUS_DONE])->one();
        if (empty($logCsvItem)) {
            return Yii::$app->response->redirect(['/error/error']);
        }
        
        return $this->render('detail', ['logCsvItem' => $logCsvItem]);
    }
}