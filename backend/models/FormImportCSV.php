<?php

namespace backend\models;

use Yii;
use yii\db\ActiveRecord;
use common\models\Quiz;
use common\models\Answer;
use common\models\Category;
use common\components\Utility;

class FormImportCSV extends \yii\db\ActiveRecord
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
        $transaction = \yii::$app->getDb()->beginTransaction();
        try {
            //insert main category
            $mainCatName = trim($data[3]);
            $categoryMain = Category::findOne(['name' => $mainCatName]);
            if (!empty($mainCatName)){
                if (!$categoryMain) {
                    $categoryMain = new Category();
                    $categoryMain->parent_id = 0;
                    $categoryMain->name = $mainCatName;
                    $categoryMain->level = 1;
                    $categoryMain->save();
                }
            }
            //insert sub1 category
            $sub1CatName = trim($data[4]);
            if (!empty($sub1CatName)) {
                $rootCatId = $categoryMain->cateory_id;
                $categorySub1 = Category::findOne(['parent_id' => $rootCatId, 'name' => $sub1CatName, 'level' => 2]);
                if (!$categorySub1) {
                    $categorySub1 = new Category();
                    $categorySub1->parent_id = $rootCatId;
                    $categorySub1->name = $sub1CatName;
                    $categorySub1->level = 2;
                    $categorySub1->save();
                }
            }
            //insert sub2 category
            $sub2CatName = trim($data[5]);
            if (!empty($sub2CatName)) {
                $sub1CatId = $categorySub1->cateory_id;
                $categorySub2 = Category::findOne(['parent_id' => $sub1CatId, 'name' => $sub2CatName, 'level' => 3]);
                if (!$categorySub2) {
                    $categorySub2 = new Category();
                    $categorySub2->parent_id = $sub1CatId;
                    $categorySub2->name = $sub2CatName;
                    $categorySub2->level = 3;
                    $categorySub2->save();
                }
            }
            //insert sub3 category
            $sub3CatName = trim($data[6]);
            if (!empty($sub3CatName)) {
                $sub2CatId = $categorySub2->cateory_id;
                $categorySub3 = Category::findOne(['parent_id' => $sub2CatId, 'name' => $sub3CatName, 'level' => 4]);
                if (!$categorySub3) {
                    $categorySub3 = new Category();
                    $categorySub3->parent_id = $sub2CatId;
                    $categorySub3->name = $sub3CatName;
                    $categorySub3->level = 4;
                    $categorySub3->save();
                }
            }
            
            //insert or update quiz
            //$quizDetail = Quiz::findOne(['quiz_id' => $data[1]]);
            //$quizModel = ($quizDetail) ? $quizDetail : new Quiz();
            $quizModel = new Quiz();
            $mainId = (!empty($mainCatName)) ? $categoryMain->cateory_id : '';
            $sub1ID = (!empty($sub1CatName)) ? $categorySub1->cateory_id : '';
            $sub2ID = (!empty($sub2CatName)) ? $categorySub2->cateory_id : '';
            $sub3ID = (!empty($sub3CatName)) ? $categorySub3->cateory_id : '';

            $quizModel->type = Quiz::TYPE_NORMAL;
            $quizModel->question = $data[7];
            $quizModel->quiz_answer = Utility::renderQuizAnswerImport($data[2]);
            $quizModel->category_id_1 = $mainId;
            $quizModel->category_id_2 = $sub1ID;
            $quizModel->category_id_3 = $sub2ID;
            $quizModel->category_id_4 = $sub3ID;
            $quizModel->quiz_year = $data[0];
            $quizModel->save();
            
            //insert or update answer
            for ($i = 1 ; $i <= 8; $i++) {
                $answerDetail = Answer::findOne(['quiz_id' => $quizModel->quiz_id, 'order' => $i]);
                //delete answer if not content answer
                if (empty($data[$i+7]) && $answerDetail) {
                    $answerDetail->delete();
                }
                //update or insert answer
                if (!empty($data[$i+7])) {
                    $modelAnswer = ($answerDetail) ? $answerDetail : new Answer();
                    $modelAnswer->quiz_id = $quizModel->quiz_id;
                    $modelAnswer->content = trim($data[$i+7]);
                    $modelAnswer->order = $i;
                    $modelAnswer->save();
                }
            }
            $transaction->commit();
            return true;
        } catch (Exception $ex) {
            $transaction->rollBack();
            return false;
        }
    }
}
