<?php
namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\QueryParamAuth;
use common\models\Category;
use frontend\models\ListCategory;

/**
 * Site controller
 */
class CategoryController extends Controller
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
     * List category
     * 
     * Auth : 
     * Create : 28-02-2017
     */
    
    public function actionList()
    {
        $request = Yii::$app->request;
        $param = $request->queryParams;
        
        $modelCategory = new ListCategory();
        $modelCategory->setAttributes($param);
        if (!$modelCategory->validate()) {
            return [
                    'status' => 400,
                    'messages' => $modelCategory->errors
                ];
        }
        
        $listMainCat = Category::find()->where(['parent_id' => 0])->all();
        if (count($listMainCat) == 0) {
            return [
                'status' => 204,
                'message' => \Yii::t('app', 'data not found')
            ];
        }
        
        $listMainCategory = [];
        $listMainSubACategory = [];
        $firtCat = Category::find()->where(['parent_id' => 0])->orderBy(['cateory_id' => SORT_ASC])->one();
        $listMainCategory[] = $firtCat->cateory_id;
        $firtSubACat = Category::find()->where(['parent_id' => $firtCat->cateory_id])->orderBy(['cateory_id' => SORT_ASC])->one();
        if ($firtSubACat && !isset($param['category_main_id'])) {
            $listMainSubACategory[] = $firtSubACat->cateory_id;
        }
        $listMainCatRequest = isset($param['category_main_id']) ? $param['category_main_id'] : $listMainCategory;
        $listSubACatRequest = isset($param['category_a_id']) ? $param['category_a_id'] : $listMainSubACategory;
        $listData = [];
        foreach ($listMainCat as $key => $value) {
            $listData['mainCategory'][] = [
                'id' => $value['cateory_id'],
                'name' => $value['name']
            ];
        }
        $listSubA = self::getListSubCat($listMainCatRequest);
        $listSubB = self::getListSubCat($listSubACatRequest);
        $listData['subA'] = [];
        $listData['subB'] = [];
        if (count($listSubA) > 0) {
            $listData['subA'] = $listSubA;
        }
        if (count($listSubB) > 0) {
            $listData['subB'] = $listSubB;
        }
        return [
            'status' => 200,
            'data' => $listData
        ];
        
    }
    
    public static function getListSubCat($listCategoryId){
        $data = [];
        if (count($listCategoryId) > 0) {
            foreach ($listCategoryId as $key => $value) {
                $listCat = Category::find()->where(['parent_id' =>$value])->orderBy(['cateory_id' => SORT_ASC])->all();
                if (count($listCat) > 0) {
                    foreach ($listCat as $key1 => $value1) {
                        $data[] = [
                            'id' => $value1['cateory_id'],
                            'name' => $value1['name']
                        ];
                    }
                }
            }
        }
        
        return $data;
    }
}