<?php
namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\QueryParamAuth;
use common\models\Category;

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
        $listMainCat = Category::find()->where(['parent_id' => 0])->all();
        if (count($listMainCat) == 0) {
            return [
                'status' => 204,
                'message' => \Yii::t('app', 'data not found')
            ];
        }
        $listData = [];
        foreach ($listMainCat as $key => $value) {
            if (self::getListSubCat($value['cateory_id'], $value['level'])) {
                $listData[] = [
                    'id' => $value['cateory_id'],
                    'name' => $value['name'],
                    'sub-cat-'.$value['level'] => self::getListSubCat($value['cateory_id'], $value['level'])
                ];
            } else {
                $listData[] = [
                    'id' => $value['cateory_id'],
                    'name' => $value['name']
                ];
            }
        }
        return [
            'status' => 200,
            'data' => $listData
        ];
        
    }
    
    public static function getListSubCat($categoryId, $level){
        $listSubCat = Category::find()->where(['parent_id' => $categoryId])->all();
        if (count($listSubCat) == 0) {
            return false;
        }
        
        $listSub = [];
        foreach ($listSubCat as $key => $value) {
            if ($value['level'] <= Category::$MAXCAT) {
                if (self::getListSubCat($value['cateory_id'], $value['level'])) {
                    $listSub[] = [
                        'id' => $value['cateory_id'],
                        'name' => $value['name'],
                        'sub-cat-'.$value['level'] => self::getListSubCat($value['cateory_id'], $value['level'])
                    ];
                } else {
                    $listSub[] = [
                        'id' => $value['cateory_id'],
                        'name' => $value['name']
                    ];
                }
            }
        }
        return $listSub;
    }
}