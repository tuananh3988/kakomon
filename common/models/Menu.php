<?php

namespace common\models;

use Yii;
use yii\helpers\Url;
use yii\db\ActiveRecord;

class Menu extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'menu';
    }

    public function renderListMenu($parent = 0, $user_tree_array = '') {
        $connection = Yii::$app->getDb();
        if (!is_array($user_tree_array))
            $user_tree_array = array();
        $sql = "SELECT `cid`, `name`, `parent` FROM `menu` WHERE 1 AND `parent` = $parent ORDER BY cid ASC";
        $query = $connection->createCommand($sql)->queryAll();
        if (count($query) > 0) {
            $user_tree_array[] = "<ul>";
            foreach ($query as $key => $value) {
                $user_tree_array[] = "<li class='select_cat' id='".$value['cid']."'>" . $value['name'] . "";
                $user_tree_array = $this->renderListMenu($value['cid'], $user_tree_array);
                $user_tree_array[] ='</li>';
            }
            $user_tree_array[] = "</ul>";
        }

        return $user_tree_array;
    }

//    public function checkChildMenu($parent){
//        $query = new \yii\db\Query();
//        $query->select(['menu.*'])
//                ->from('menu');
//        $query->where(['parent' => $parent]);
//        if (count($query->all()) > 0) {
//            return TRUE;
//        }
//        return FALSE;
//    }
}
