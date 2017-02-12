<?php

namespace common\models;

use Yii;
use yii\helpers\Url;
use yii\db\ActiveRecord;

class Category extends \yii\db\ActiveRecord {

    public $idParent;
    public $type;
    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'category';
    }
    
    public static $TYPE = [0, 1, 2];
    public static $MAXCAT = 4;




    public function rules() {
        return [
            [['name'], 'required', 'message' => \Yii::t('app', 'required')],
            ['idParent', 'validateIdParent'],
            ['type', 'validateType'],
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function safeAttributes()
    {
        $safe = parent::safeAttributes();
        return array_merge($safe, $this->extraFields());
    }
    
    /**
     * @inheritdoc
     */
    public function extraFields()
    {
        return ['idParent', 'type'];
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'type' => 'Type',
            'idParent' => 'idParent'
        ];
    }
    
    /*
     * Auth :
     * 
     * Create : 11-02-2017
     */
    public function renderListMenu($parent = 0, $user_tree_array = '') {
        $connection = Yii::$app->getDb();
        if (!is_array($user_tree_array))
            $user_tree_array = array();
        $sql = "SELECT `id`, `name`, `parent` FROM `category` WHERE 1 AND `parent` = $parent ORDER BY id ASC";
        $query = $connection->createCommand($sql)->queryAll();
        if (count($query) > 0) {
            $user_tree_array[] = "<ul>";
            foreach ($query as $key => $value) {
                $user_tree_array[] = "<li class='select_cat' id='".$value['id']."'>" . $value['name'] . "";
                $user_tree_array = $this->renderListMenu($value['id'], $user_tree_array);
                $user_tree_array[] ='</li>';
            }
            $user_tree_array[] = "</ul>";
        }

        return $user_tree_array;
    }
    
    /*
     * validate Parent id
     * 
     * Auth : 
     * Create : 11-02-2017
     */
    
    public function validateIdParent($attribute){
        if ($this->type == 1) {
            $category = Category::findOne(['id' => $this->$attribute]);
            if (!$category) {
                $this->addError($attribute, \Yii::t('app', 'exit', ['attribute' => $this->attributeLabels()[$attribute]]));
            } else {
                if ($category->level == self::$MAXCAT) {
                    $this->addError($attribute, \Yii::t('app', 'can not create category', ['attribute' => $this->attributeLabels()[$attribute]]));
                }
            }
        }
    }
    
    /*
     * validate type
     * 
     * Auth : 
     * Create : 11-02-2017
     */
    public function validateType($attribute){
        if (!in_array($this->$attribute, self::$TYPE)) {
            $this->addError($attribute, \Yii::t('app', 'illegal', ['attribute' => $this->attributeLabels()[$attribute]]));
        }
    }
    
    /*
     * Add category
     * 
     * Auth : 
     * Create : 11-02-2017
     */
    
    public function addCategory(){
        $transaction = \yii::$app->getDb()->beginTransaction();
        try {
            //update category
            if ($this->type == 0) {
                $this->upd_user = Yii::$app->user->identity->id;
                $this->update_date = date('Y-m-d H:i:s');
                $this->save(false);
                return $this->id;
            }
            //add sub category
            if ($this->type == 1) {
                $parentCat = Category::findOne(['id' => $this->idParent]);
                $category = new Category();
                $category->name = $this->name;
                $category->parent = $this->idParent;
                $category->level = $parentCat->level + 1;
                $category->create_user = Yii::$app->user->identity->id;
                $category->create_date = date('Y-m-d H:i:s');
                $category->save();
                return $category->id;
            }
            // add root category
            if ($this->type == 2) {
                $categoryRoot = new Category();
                $categoryRoot->name = $this->name;
                $categoryRoot->parent = 0;
                $categoryRoot->level = 1;
                $categoryRoot->create_user = Yii::$app->user->identity->id;
                $categoryRoot->create_date = date('Y-m-d H:i:s');
                $categoryRoot->save();
                return $categoryRoot->id;
            }
        } catch (Exception $exc) {
            $transaction->rollBack();
            return FALSE;
        }
    }
}
