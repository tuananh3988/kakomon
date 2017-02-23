<?php
namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\Response;
use common\models\Utility;
use common\models\WpFormat;
use common\models\PostView;
use common\models\SearchSumary;
use common\models\Wp32Popularpostsdata;
use common\models\Wp32Popularpostssummary;
use common\models\FavoritePost;
use common\models\FavoriteSumary;
/**
 * Site controller
 */
class MemberController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
//            'access' => [
//                'class' => AccessControl::className(),
//                'rules' => [
//                    [
//                        'actions' => ['list', 'detail', 'ranking', 'auto-complete', 'favorite', 'unfavorite'],
//                        'allow' => true,
//                        'roles' => ['?'],
//                    ],
//                ],
//            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'detail' => ['get'],
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

    
    public function actionDetail()
    {
        return [
            'status' => 1
        ];
    }
    


}
