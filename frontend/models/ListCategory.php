<?php

namespace frontend\models;

use Yii;
use yii\base\Model;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
/**
 * ContactForm is the model behind the contact form.
 */
class ListCategory extends \yii\db\ActiveRecord
{
    public $category_main_id;
    public $category_a_id;
    public $category_b_id;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'category';
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['category_main_id', 'category_a_id', 'category_b_id'], 'validateType'],
            [['category_main_id', 'category_a_id', 'category_b_id', 'created_date', 'updated_date'], 'safe'],
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
            'category_main_id' => 'Category Main Id',
            'category_a_id' => 'Category A Id',
            'category_b_id' => 'Category B Id'
        ];
    }
    
    /*
     * validate unique mail
     * 
     * Auth : 
     * Create : 03-01-2017
     */
    public function validateType($attribute)
    {
        if (!$this->hasErrors()) {
            if (!is_array($this->$attribute)) {
                $this->addError($attribute, 'Data must be an array');
            }
        }
    }
}