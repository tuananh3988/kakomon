<?php

namespace frontend\models;

use Yii;
use yii\base\Model;
use yii\web\UploadedFile;
/**
 * ContactForm is the model behind the contact form.
 */
class FormUpload extends Model
{
    public $file;

    public function rules()
    {
        return [
            [['file'], 'required'],
            [['file'], 'validateExtensions'],
            [['file'], 'validateMaxSize'],
            [['file'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return[
            'file' => 'File upload',
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
        return ['file'];
    }
    
    /*
     * validate validateExtensions
     * 
     * Auth : 
     * Create : 09-03-2017 
     */
    
    public function validateExtensions($attribute){
        $infoFile = $this->$attribute;
        if (!in_array(pathinfo($infoFile['file']['name'], PATHINFO_EXTENSION), ['png', 'jpg', 'jpeg'])) {
            $this->addError($attribute, \Yii::t('app', 'extensions',['attribute' => $this->attributeLabels()[$attribute]]));
        }
    }
    
    /*
     * validate validateRequired
     * 
     * Auth : 
     * Create : 09-03-2017 
     */
    
    public function validateMaxSize($attribute){
        $infoFile = $this->$attribute;
        if ($infoFile['file']['size'] > 8*1024*1024) {
            $this->addError($attribute, \Yii::t('app', 'maxSize',['attribute' => $this->attributeLabels()[$attribute]]));
        }
    }
}