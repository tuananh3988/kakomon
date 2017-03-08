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
            [['file'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, jpeg'],
            [['file'], 'file', 'extensions' => 'png, jpg, jpeg', 'maxSize'=> 8*1024*1024,
                'tooBig' => \Yii::t('app', 'file size upload'), 'checkExtensionByMimeType' => false ,
                'wrongExtension' => \Yii::t('app', 'Extension png, jpg, jpeg')],
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
}