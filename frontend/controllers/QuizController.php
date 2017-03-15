<?php
namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\QueryParamAuth;
use frontend\models\Question;
use common\models\Category;

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
        
        $modelQuiz = new Question();
        $modelQuiz->setAttributes($param);
        if (!$modelQuiz->validate()) {
            return [
                    'status' => 400,
                    'messages' => $modelQuiz->errors
                ];
        }
        //get firt category
        $firtCat = Category::find()->where(['parent_id' => 0])->orderBy(['cateory_id' => SORT_ASC])->one();
        if (!$firtCat) {
            return [
                'status' => 204,
                'message' => \Yii::t('app', 'data not found')
            ];
        }
        $modelQuiz->category_main_id =  !empty($param['category_main_id']) ? $param['category_main_id'] : $firtCat['cateory_id'];
        
        var_dump($modelQuiz->getListQuiz($limit, $offset));die;
    }
}