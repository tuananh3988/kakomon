<?php

namespace backend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use backend\models\LoginForm;
use yii\filters\VerbFilter;
use common\models\Category;
use yii\web\Response;
use yii\web\Session;

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
        $session = Yii::$app->session;
        $request = Yii::$app->request;
        $category = new Category();
        $listCategory = $category->renderListMenu();
        //find one first record menu
        if (isset($session['category_id']) && $session['category_id'] != NULL) {
            $firstCategory = Category::findOne(['id' => $session->get('category_id')]);
        } else {
            $firstCategory = Category::find()->orderBy(['id' => SORT_ASC])->one();
            if (!$firstCategory) {
                $firstCategory = $category;
            }
        }
        //set type and idParent
        if ($firstCategory) {
            $firstCategory->type = 0;
            $firstCategory->idParent = $firstCategory->id;
        }
        //request post
        if ($request->isPost) {
            $dataPost = $request->Post();
            if ($dataPost['Category']['type'] == 0) {
                $firstCategory = Category::findOne(['id' => $dataPost['Category']['id']]);
            }
            $firstCategory->load($dataPost);
            if ($firstCategory->validate()) {
                $id = $firstCategory->addCategory();
                if ($id) {
                    $session->set('category_id', $id);
                    $message ='';
                    if ($firstCategory->type == 0) {
                        $message = 'Your update successfully category!';
                    } elseif($firstCategory->type == 1){
                        $message = 'You successfully created sub category!';
                    } elseif($firstCategory->type == 2) {
                        $message = 'You successfully created category!';
                    }
                    $session->setFlash('sucess',$message);
                    return Yii::$app->response->redirect(['/category/index']);
                } else {
                    return Yii::$app->response->redirect(['/site/error']);
                }
            } 
        }
        $breadCrumbs = $this->renderBreadCrumbs($firstCategory->id);
        if ($firstCategory->id == NULL) {
            $firstCategory->type = 2;
            $breadCrumbs = 'Untitled';
        }
        return $this->render('index', [
            'listCategory' => $listCategory,
            'firstCategory' => $firstCategory,
            'breadCrumbs' => $breadCrumbs
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
        $detail = Category::findOne(['id' => $id]);
        if ($detail) {
            $result['success'] = 1;
            $result['data'] = [
                'id' => $detail->id,
                'name' => $detail->name,
                'level' => $detail->level,
                'breadcrumbs' => $this->renderBreadCrumbs($id)
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
    
    public function renderBreadCrumbs($id){
        $textBreadCrumbs = '';
        $result = [];
        $menu = Category::findOne(['id' => $id]);
        if ($menu) {
            $parentId = $menu->parent;
            $result[] = '<span class="kv-crumb-active">' . $menu->name . '</span>';
            while ($parentId > 0) {
                $query = Category::findOne(['id' => $parentId]);
                if ($query) {
                    $result[] = $query->name;
                    $parentId = $query->parent;
                }
            }
        }
        if (count($result) > 0) {
            krsort($result);
            $i = 0;
            foreach ($result as $key => $value) {
                $i ++;
                if ($i == count($result)) {
                    $textBreadCrumbs .= $value;
                } else {
                    $textBreadCrumbs .= $value . " » ";
                }
            }
        }
        return $textBreadCrumbs;
    }
}
