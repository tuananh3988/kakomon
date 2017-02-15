<?php

namespace common\components;

use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\imagine\Image;
use yii\web\UploadedFile;

class Utility extends Component
{
    
    public function uploadImages($infoImages, $type, $idParent, $id = null){
        $dirParent = Yii::$app->params['imgPath'] . 'uploads';
        if (!is_dir($dirParent)) {
            mkdir($dirParent, 0777);
        }
        $dir = Yii::$app->params['imgPath'] . Yii::$app->params['imgUpload'][$type];
        if (!is_dir($dir)) {
            mkdir($dir, 0777);
        }
        $path = Yii::$app->params['imgPath'] . Yii::$app->params['imgUpload'][$type] . $type .'_' . $idParent. '.jpg';
        if ($id) {
            $path = Yii::$app->params['imgPath'] . Yii::$app->params['imgUpload'][$type] . $type .'_' . $idParent. '_' . $id . '.jpg';
        }
        
        $infoImages->saveAs($path);
    }
    
    public static function getImage($type, $idParent, $id = null)
    {
        $image = '';
        $folder = ['question', 'answer'];
        if (!in_array($type, $folder)) {
            return $image;
        }
        $path = Yii::$app->params['imgPath'] . Yii::$app->params['imgUpload'][$type] . $type .'_' . $idParent. '.jpg';
        if ($id) {
            $path = Yii::$app->params['imgPath'] . Yii::$app->params['imgUpload'][$type] . $type .'_' . $idParent. '_' . $id . '.jpg';
        }
        if (file_exists($path)) {
            $type = pathinfo($path, PATHINFO_EXTENSION);
            $data = file_get_contents($path);
            $image = 'data:image/' . $type . ';base64,' . base64_encode($data);
        }
        
        return $image;
    }
}