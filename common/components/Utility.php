<?php

namespace common\components;

use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\imagine\Image;
use yii\web\UploadedFile;
use common\models\Quiz;

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
    
    /*
     * Remove images
     */
    
    public function removeImages($type, $idParent, $id = null){
        $folder = ['question', 'answer'];
        if (!in_array($type, $folder)) {
            return $image;
        }
        $path = Yii::$app->params['imgPath'] . Yii::$app->params['imgUpload'][$type] . $type .'_' . $idParent. '.jpg';
        if ($id) {
            $path = Yii::$app->params['imgPath'] . Yii::$app->params['imgUpload'][$type] . $type .'_' . $idParent. '_' . $id . '.jpg';
        }
        if (file_exists($path)) {
            unlink($path);
        }
        return TRUE;
    }
    
    /*
     * check exit file
     */
    
    public function checkExitImages($type, $idParent, $id = null){
        $folder = ['question', 'answer'];
        if (!in_array($type, $folder)) {
            return $image;
        }
        $path = Yii::$app->params['imgPath'] . Yii::$app->params['imgUpload'][$type] . $type .'_' . $idParent. '.jpg';
        if ($id) {
            $path = Yii::$app->params['imgPath'] . Yii::$app->params['imgUpload'][$type] . $type .'_' . $idParent. '_' . $id . '.jpg';
        }
        if (file_exists($path)) {
            return TRUE;
        }
        return FALSE;
    }
    
    /*
     * render quiz answer
     * 
     * Auth : 
     * Create : 28-02-2017
     */
    
    public static function renderQuizAnswer($data)
    {
        $dataQuizAnswer = Quiz::QUIZ_ANSWER;
        for ($i = 1; $i <= 8; $i++) {
            if (!empty($data['Quiz']) && $data['Quiz']['quiz_answer'.$i] == 1) {
                $dataQuizAnswer = substr_replace($dataQuizAnswer, '1', ($i-1), 1);
            }
        }
        return $dataQuizAnswer;
    }
    
    /*
     * render offset
     * 
     * Auth :
     * Created : 03-03-2017
     */
    
    public static function renderOffset($total, $limit, $offset)
    {
        $offsetReturn = 0;
        if (($limit + $offset +1) <= $total) {
            $offsetReturn = $limit + $offset;
        }
        
        return $offsetReturn;
    }
    
    /*
     * render quiz answer for import csv
     * 
     * Auth : 
     * Create : 03-03-2017
     */
    
    public static function renderQuizAnswerImport($data)
    {
        $listAns  = explode(".", $data);
        $dataQuizAnswer = Quiz::QUIZ_ANSWER;
        if (count($listAns) > 0){
            for ($i = 0; $i <count($listAns); $i++) {
                $dataQuizAnswer = substr_replace($dataQuizAnswer, '1', ($listAns[$i] - 1), 1);
            }
        }
        return $dataQuizAnswer;
    }
}