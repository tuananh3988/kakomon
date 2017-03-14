<?php

namespace console\controllers;
use Yii;
use common\models\LogCsv;
use common\components\Utility;
use backend\models\FormImportCSV;

class CsvController extends \yii\console\Controller
{
    
    public function actionUpload()
    {
        $modelLogCsv = new LogCsv();
        $model = new FormImportCSV();
        $listLogCsv = $modelLogCsv->findAll(['status' => LogCsv::STATUS_WAIT]);
        $error = [];
        if (count($listLogCsv) > 0) {
            foreach ($listLogCsv as $key => $value) {
                $lineErrors = 0;
                $messageErrors = [];
                $fileName = $value->file_name.'.csv';
                $dataLog = $modelLogCsv->findOne(['log_id' => $value->log_id]);
                // check file exit
                if (!Utility::checkExitCsv('process', $fileName)) {
                    $error =[
                        'errorCode' => 0,
                        'message' => 'File not found please check again!',
                        'data' => ''
                    ];
                    
                    $dataLog->status = LogCsv::STATUS_DONE;
                    $dataLog->content_log = json_encode($error);
                    $dataLog->save();
                    continue;
                }
                
                //update status
                $dataLog->status = LogCsv::STATUS_PROCESS;
                $dataLog->save();
                
                //unzip folder
                $fileNameFolder = $value->file_name.'.tar';
                $flagFolder = Utility::checkExitCsv('process', $fileNameFolder);
                if ($flagFolder) {
                    Utility::unzipFile($fileNameFolder, $value->file_name);
                }
                //read and insert file csv
                $handle = fopen(Yii::$app->params['imgPath'] . Yii::$app->params['csvUpload']['process'] . $fileName, "r");
                while (($fileop = fgetcsv($handle, 1000, ",")) !== false) {
                    if ($fileop[0] != 'Year') {
                        $lineErrors++;
                        if (!$model->saveData($fileop, $value->file_name, $flagFolder)) {
                            $messageErrors[] = $lineErrors;
                        }
                    }
                }
                fclose($handle);
                //move file csv and folder images to folder done
                
                Utility::moveToFolderDone($value->file_name);
                
                $error =[
                    'errorCode' => 0,
                    'message' => 'Insert success!',
                    'data' => $messageErrors
                ];
                 //update status for done
                $dataLog->status = LogCsv::STATUS_DONE;
                $dataLog->content_log = json_encode($error);
                $dataLog->save();
            }
        }
        
    }
}