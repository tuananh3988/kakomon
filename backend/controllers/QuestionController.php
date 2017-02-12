<?php

namespace backend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use backend\models\LoginForm;
use yii\filters\VerbFilter;
use common\models\Category;
use common\models\Question;
use yii\web\Response;

class QuestionController extends Controller {

    public function behaviors() {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['index', 'save', 'getsubcategory'],
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

    public function actionIndex() {
        return $this->render('index');
    }
    
    /*
     * Auth : 
     * 
     * Method :
     * Create : 09-02-2017
     */
    
    public function actionSave() {
        $request = Yii::$app->request;
        $rootCat = Category::find()->select('name')->where(['level' => 1])->indexBy('id')->column();
        $question = new Question();
        if ($request->isPost) {
            $dataPost = $request->Post();
            var_dump($dataPost);die;
        }
        return $this->render('save', [
            'rootCat' => $rootCat,
            'question' => $question
        ]);
    }
    /*
     * Auth : 
     * 
     * Method :
     * Create : 12-02-2017
     */
    
    public function actionGetsubcategory() {
        $result = [];
        $request = Yii::$app->request;
        $id = $request->getQueryParam('id');
        $level = $request->getQueryParam('level');
        $subCat = Category::find()->select('name')->where(['parent' => $id])->indexBy('id')->column();
        $result['success'] = 1;
        $data = '<option value="">Select sub'.$level.' category</option>';
        if (count($subCat) > 0) {
            foreach ($subCat as $key => $value) {
                $data .= '<option value="'.$key.'">'.$value.'</option>';
            }
        }
        $result['data'] = $data;
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return $result;
    }
}
