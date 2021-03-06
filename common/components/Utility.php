<?php

namespace common\components;

use Yii;
use yii\helpers\Url;
use yii\base\Component;
use common\models\Quiz;
use yii\helpers\FileHelper;

class Utility extends Component
{
    
    public function uploadImages($infoImages, $type, $idParent, $id = null){
        $dirParent = Url::to(Yii::$app->params['imgPath']) . 'uploads';
        if (!is_dir($dirParent)) {
            mkdir($dirParent, 0777);
        }
        $dir = Url::to(Yii::$app->params['imgPath']) . Yii::$app->params['imgUpload'][$type];
        if (!is_dir($dir)) {
            mkdir($dir, 0777);
        }
        $path = Url::to(Yii::$app->params['imgPath']) . Yii::$app->params['imgUpload'][$type] . $type .'_' . $idParent. '.jpg';
        if ($id) {
            $path = Url::to(Yii::$app->params['imgPath']) . Yii::$app->params['imgUpload'][$type] . $type .'_' . $idParent. '_' . $id . '.jpg';
        }
        
        $infoImages->saveAs($path);
    }
    
    public static function getImage($type, $idParent, $id = null, $flagReturn = false)
    {
        $image = '';
        $folder = ['question', 'answer', 'member'];
        if (!in_array($type, $folder)) {
            return $image;
        }
        $path = Url::to(Yii::$app->params['imgPath']) . Yii::$app->params['imgUpload'][$type] . $type .'_' . $idParent. '.jpg';
        $pathApi = Yii::$app->urlManagerBackend->baseUrl . '/' .  Yii::$app->params['imgUpload'][$type] . $type .'_' . $idParent. '.jpg';
        if ($id) {
            $path = Url::to(Yii::$app->params['imgPath']) . Yii::$app->params['imgUpload'][$type] . $type .'_' . $idParent. '_' . $id . '.jpg';
            $pathApi = Yii::$app->urlManagerBackend->baseUrl.  '/' . Yii::$app->params['imgUpload'][$type] . $type .'_' . $idParent. '_' . $id . '.jpg';
        }
        if (file_exists($path)) {
            $type = pathinfo($path, PATHINFO_EXTENSION);
            $data = file_get_contents($path);
            $image = 'data:image/' . $type . ';base64,' . base64_encode($data);
            if ($flagReturn) {
                return $pathApi;
            }
        }
        
        return $image;
    }
    
    /*
     * Remove images
     */
    
    public function removeImages($type, $idParent, $id = null){
        $folder = ['question', 'answer', 'member'];
        if (!in_array($type, $folder)) {
            return $image;
        }
        $path = Url::to(Yii::$app->params['imgPath']) . Yii::$app->params['imgUpload'][$type] . $type .'_' . $idParent. '.jpg';
        if ($id) {
            $path = Url::to(Yii::$app->params['imgPath']) . Yii::$app->params['imgUpload'][$type] . $type .'_' . $idParent. '_' . $id . '.jpg';
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
        $folder = ['question', 'answer', 'member'];
        if (!in_array($type, $folder)) {
            return FALSE;
        }
        $path = Url::to(Yii::$app->params['imgPath']) . Yii::$app->params['imgUpload'][$type] . $type .'_' . $idParent. '.jpg';
        if ($id) {
            $path = Url::to(Yii::$app->params['imgPath']) . Yii::$app->params['imgUpload'][$type] . $type .'_' . $idParent. '_' . $id . '.jpg';
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
     * render quiz answer
     * 
     * Auth : 
     * Create : 28-02-2017
     */
    
    public static function exportQuizAnswer($data)
    {
        $dataAns = [];
        $dataQuizAnswer = str_split($data);
        for ($i = 0; $i < count($dataQuizAnswer); $i++) {
            if ($dataQuizAnswer[$i] == 1) {
                $dataAns[] = $i+1;
            }
        }
        return $dataAns;
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
    
    /*
     * upload image for api
     * 
     * Auth :
     * Create : 09-03-2017
     */
    
    public function uploadImagesForApi($infoImages, $type, $idParent, $id = null){
        $dirParent = Url::to(Yii::$app->params['imgPath']) . 'uploads';
        if (!is_dir($dirParent)) {
            mkdir($dirParent, 0777);
        }
        $dir = Url::to(Yii::$app->params['imgPath']) . Yii::$app->params['imgUpload'][$type];
        if (!is_dir($dir)) {
            mkdir($dir, 0777);
        }
        $path = Url::to(Yii::$app->params['imgPath']) . Yii::$app->params['imgUpload'][$type] . $type .'_' . $idParent. '.jpg';
        
        move_uploaded_file($infoImages["file"]["tmp_name"], $path);
    }
    
    /*
     * upload image for api
     * 
     * Auth :
     * Create : 09-03-2017
     */
    
    public function uploadImagesQuizForApi($infoImages, $type, $key, $idParent, $id = null){
        $dirParent = Url::to(Yii::$app->params['imgPath']) . 'uploads';
        if (!is_dir($dirParent)) {
            mkdir($dirParent, 0777);
        }
        $dir = Url::to(Yii::$app->params['imgPath']) . Yii::$app->params['imgUpload'][$type];
        if (!is_dir($dir)) {
            mkdir($dir, 0777);
        }
        $path = Url::to(Yii::$app->params['imgPath']) . Yii::$app->params['imgUpload'][$type] . $type .'_' . $idParent. '.jpg';
        if ($id) {
            $path = Url::to(Yii::$app->params['imgPath']) . Yii::$app->params['imgUpload'][$type] . $type .'_' . $idParent. '_' . $id . '.jpg';
        }
        
        move_uploaded_file($infoImages[$key]["tmp_name"], $path);
    }
    
    /*
     * upload csv
     * 
     * Auth :
     * Create : 09-03-2017
     */
    
    public static function uploadCsv($infoCsv, $type, $name){
        $dirParent = Url::to(Yii::$app->params['imgPath']) . 'csvUpload';
        if (!is_dir($dirParent)) {
            mkdir($dirParent, 0777);
        }
        $dir = Url::to(Yii::$app->params['imgPath']) . Yii::$app->params['csvUpload'][$type];
        if (!is_dir($dir)) {
            mkdir($dir, 0777);
        }
        $path = Url::to(Yii::$app->params['imgPath']) . Yii::$app->params['csvUpload'][$type] . $name;
        
        $infoCsv->saveAs($path);
    }
    
    /*
     * check exit file csv
     */
    
    public static function checkExitCsv($type, $fileName){
        $path = Url::to(Yii::$app->params['imgPath']) . Yii::$app->params['csvUpload'][$type] . $fileName;
        
        if (file_exists($path)) {
            return TRUE;
        }
        return FALSE;
    }
    
    /*
     * unzip folde
     * 
     * Auth : 
     * Create : 14-03-2017
     */
    
    public static function unzipFile($fileName, $name)
    {
        $file = Url::to(Yii::$app->params['imgPath']) . Yii::$app->params['csvUpload']['process'] . $fileName;
        $zip = new \ZipArchive();
        $zipped = $zip->open($file);
        $path = pathinfo(realpath($file), PATHINFO_DIRNAME) . '/' . $name;
        if ($zipped) {
        //if yes then extract it to the said folder
          $extract = $zip->extractTo($path);
          $zip->close();
        }
    }
    
    /*
     * Find file in folder
     * 
     * Auth : 
     * Create : 14-03-2017
     */
    
    public static function getImagesInFolder($fileName, $path){
        if (!is_dir($path)) {
            return [];
        }
        return FileHelper::findFiles($path, ['only' => [$fileName. '.' . '*']]);
    }
    
    /*
     * move images
     * 
     * Auth :
     * Create : 17-03-2017
     */
    
    public static function moveImages($fileName, $type, $pathFolder, $idParent, $id = null){
        $dirParent = Url::to(Yii::$app->params['imgPath']) . 'uploads';
        if (!is_dir($dirParent)) {
            mkdir($dirParent, 0777);
        }
        $dir = Url::to(Yii::$app->params['imgPath']) . Yii::$app->params['imgUpload'][$type];
        if (!is_dir($dir)) {
            mkdir($dir, 0777);
        }
        $pathTo = Url::to(Yii::$app->params['imgPath']) . Yii::$app->params['imgUpload'][$type] . $type . '_' . $idParent . '.jpg';
        if ($id) {
            $pathTo = Url::to(Yii::$app->params['imgPath']) . Yii::$app->params['imgUpload'][$type] . $type .'_' . $idParent. '_' . $id . '.jpg';
        }
        $pathFrom = $fileName;
        rename($pathFrom, $pathTo);
    }
    
    /*
     * move folder done
     * 
     * Auth :
     * Create : 17-03-2017
     */
    public static function moveToFolderDone($fileName){
        $dirParent = Url::to(Yii::$app->params['imgPath']) . 'csvUpload';
        if (!is_dir($dirParent)) {
            mkdir($dirParent, 0777);
        }
        $dir = Url::to(Yii::$app->params['imgPath']) . Yii::$app->params['csvUpload']['done'];
        if (!is_dir($dir)) {
            mkdir($dir, 0777);
        }
        //move file csv
        $pathToCsv = Url::to(Yii::$app->params['imgPath']) . Yii::$app->params['csvUpload']['done'] . $fileName . '.csv';
        $pathFromCsv = Url::to(Yii::$app->params['imgPath']) . Yii::$app->params['csvUpload']['process'] .$fileName . '.csv';
        rename($pathFromCsv, $pathToCsv);
        //move file tar
        $fileNameFolder = $fileName.'.tar';
        if ( self::checkExitCsv('process', $fileNameFolder)) {
            $pathToTar = Url::to(Yii::$app->params['imgPath']) . Yii::$app->params['csvUpload']['done'] . $fileName . '.tar';
            $pathFromTar = Url::to(Yii::$app->params['imgPath']) . Yii::$app->params['csvUpload']['process'] .$fileName . '.tar';
            rename($pathFromTar, $pathToTar);
        }
    }
}