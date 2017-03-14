<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use common\models\Quiz;
/**
 * This is the model class for table "category".
 *
 * @property integer $cateory_id
 * @property integer $parent_id
 * @property string $name
 * @property integer $level
 * @property string $created_date
 * @property string $updated_date
 */
class Category extends \yii\db\ActiveRecord
{
    public $idParent;
    public $type;
    public $flagDelete;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'category';
    }

    public static $TYPE = [0, 1, 2];
    public static $MAXCAT = 4;
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
//            [['cateory_id'], 'required'],
            [['parent_id', 'level'], 'integer'],
            [['created_date', 'updated_date'], 'safe'],
            [['name'], 'string', 'max' => 255],
            [['name'], 'required', 'message' => \Yii::t('app', 'required')],
            ['idParent', 'validateIdParent'],
            ['type', 'validateType'],
        ];
    }

    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                          ActiveRecord::EVENT_BEFORE_INSERT => ['created_date'],
                          ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_date'],
                ],
                'value' => date('Y-m-d H:i:s'),
            ],
        ];
    }
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'cateory_id' => 'Cateory ID',
            'parent_id' => 'Parent ID',
            'name' => 'Name',
            'level' => 'Level',
            'created_date' => 'Created Date',
            'updated_date' => 'Updated Date',
            'type' => 'Type',
            'idParent' => 'idParent',
            'flagDelete' => 'flagDelete'
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
        return ['idParent', 'type', 'flagDelete'];
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
        $sql = "SELECT `cateory_id`, `name`, `parent_id` FROM `category` WHERE 1 AND `parent_id` = $parent ORDER BY cateory_id ASC";
        $query = $connection->createCommand($sql)->queryAll();
        if (count($query) > 0) {
            $user_tree_array[] = "<ul>";
            foreach ($query as $key => $value) {
                $user_tree_array[] = "<li class='select_cat' id='".$value['cateory_id']."'>" . $value['name'] . "";
                $user_tree_array = $this->renderListMenu($value['cateory_id'], $user_tree_array);
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
            $category = Category::findOne(['cateory_id' => $this->$attribute]);
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
        //update category
        if ($this->type == 0) {
            $this->updated_date = date('Y-m-d H:i:s');
            $this->save(false);
            return $this->cateory_id;
        }
        //add sub category
        if ($this->type == 1) {
            $parentCat = Category::findOne(['cateory_id' => $this->idParent]);
            $category = new Category();
            $category->name = $this->name;
            $category->parent_id = $this->idParent;
            $category->level = $parentCat->level + 1;
            $category->save();
            return $category->cateory_id;
        }
        // add root category
        if ($this->type == 2) {
            $categoryRoot = new Category();
            $categoryRoot->name = $this->name;
            $categoryRoot->parent_id = 0;
            $categoryRoot->level = 1;

            $categoryRoot->save();
            return $categoryRoot->cateory_id;
        }
    }
    
    /*
     * get Detail name categoty
     * 
     * Auth: 
     * Create : 15-02-2017
     */
    
    public static function getDetailNameCategory($id){
        $name = '';
        if ($id) {
            $category = Category::findOne(['cateory_id'=> $id]);
            $name = $category->name;
        }
        return $name;
    }
    
    /*
     * get sub category
     * 
     * Auth : 
     * Create : 12-02-2017
     */
    
    public static function getsubcategory($id) {
        $subCat = Category::find()->select('name')->where(['parent_id' => $id])->indexBy('cateory_id')->column();
        $data = [];
        if (count($subCat) > 0) {
            $data = $subCat;
        }
        return $data;
    }
    
    /*
     * Check quiz
     * 
     * Auth : 
     * Create : 20-02-2017
     */
    
    public static function checkQuizWithCategory($catId){
        $catChild = Category::find()->where(['parent_id' => $catId])->all();
        $quiz = Quiz::getListCategoryById($catId);
        if ((count($catChild) > 0) || (count($quiz) > 0)) {
            return FALSE;
        }
        return TRUE;
    }
    
    /*
     * Get list timeline help
     * 
     * Auth : 
     * Create : 08-03-2017
     */
    
    public function getListTimelineHelp($catId, $flag = false){
        $query = new \yii\db\Query();
        $query->select(['category.cateory_id', 'quiz.*', 'member.name', 'member.member_id', 'activity.content as content_activity'])
                ->from('category');
        $query->join('INNER JOIN', 'quiz', 'quiz.category_main_id = category.cateory_id');
        $query->join('INNER JOIN', 'activity', 'quiz.quiz_id = activity.quiz_id');
        $query->join('INNER JOIN', 'member', 'member.member_id = activity.member_id');
        $query->andWhere(['category.cateory_id' => $catId]);
        $query->andWhere(['quiz.delete_flag' => 0]);
        $query->andWhere(['activity.type' => Activity::TYPE_HELP]);
        $query->andWhere(['activity.status' => Activity::STATUS_ACTIVE]);
        if ($flag) {
            return $query->count();
        }
        return $query->all();
    }
    
}
