<?php
namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\QueryParamAuth;
use frontend\models\Quiz;

/**
 * Site controller
 */
class QuizController extends Controller
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
                    'list' => ['search']
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
    
    
    public function actionSearch()
    {
        $request = Yii::$app->request;
        $param = $request->queryParams;
        $limit = isset($param['limit']) ? $param['limit'] : Yii::$app->params['limit']['quiz'];
        $offset = isset($param['offset']) ? $param['offset'] : Yii::$app->params['offset']['quiz'];
        
        $modelQuiz = new Quiz();
        $modelQuiz->setAttributes($param);
        if (!$modelQuiz->validate()) {
            return [
                    'status' => 400,
                    'messages' => $modelQuiz->errors
                ];
        }
        

    }
}