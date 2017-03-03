<?php

namespace backend\models;

use Yii;
use yii\base\Model;

class FormImportCSV extends Model
{

    public $file;

    public function rules()
    {
        return [
            [['file'], 'required'],
            [['file'], 'file', 'extensions' => 'csv', 'maxSize'=> 8*1024*1024,
                'tooBig' => \Yii::t('app', 'file size upload'), 'checkExtensionByMimeType' => false ,
                'wrongExtension' => \Yii::t('app', 'Extension CSV')],
            [['file'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return[
            'file' => 'CSV file',
        ];
    }
    
    /*
     * Update or add 
     * 
     * Auth Nguyen Van Hien <hiennv6244@seta-asia.com.vn>
     * Date 23/03/2016
     */
    
    public function saveData($data)
    {
        var_dump($data[0]);die;
    }
}
