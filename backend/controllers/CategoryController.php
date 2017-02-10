<?php

namespace backend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use backend\models\LoginForm;
use yii\filters\VerbFilter;
use common\models\Menu;
use yii\web\Response;

class CategoryController extends Controller {

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

    public function actionIndex() {
        $menu = new Menu();
        $listMenu = $menu->renderListMenu();
        //find one first record menu
        $firstMenu = Menu::find()->orderBy(['cid' => SORT_ASC])->one();
        return $this->render('index', [
            'listMenu' => $listMenu,
            'firstMenu' => $firstMenu
        ]);
    }

    /*
     * Auth : 
     * 
     * Create Date : 10-02-2017
     */
    
    public function actionDetail(){
        $result = [];
        $request = Yii::$app->request;
        $id = $request->getQueryParam('id');
        $detail = Menu::findOne(['cid' => $id]);
        $this->renderCrumb($id);
        if ($detail) {
            $result['success'] = 1;
            $result['data'] = [
                'cid' => $detail->cid,
                'name' => $detail->name,
                'level' => $detail->level
            ];
        } else {
            $result['success'] = 0;
            $result['message'] = 'Not found data';
        }
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return $result;
    }
    
    /*
     * Auth : 
     * 
     * Create Date : 10-02-2017
     */
    
    public function renderCrumb($id){
        $textCrumb = '';
        $result = [];
        $menu = Menu::findOne(['cid' => $id]);
        if ($menu) {
            $parentId = $menu->parent;
            $result[] = '<span class="kv-crumb-active">' . $menu->name . '</span>';
            while ($parentId > 0) {
                $query = Menu::findOne(['cid' => $parentId]);
                if ($query) {
                    $result[] = $query->name;
                    $parentId = $query->parent;
                }
            }
        }
        if (count($result) > 0) {
            krsort($result);
            foreach ($result as $key => $value) {
                $textCrumb .= $value . " » ";
            }
        }
        var_dump($textCrumb);die;
    }
}
