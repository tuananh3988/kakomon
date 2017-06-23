<?php

namespace console\controllers;
use Yii;
use common\models\ExamHistory;
class ExamController extends \yii\console\Controller
{
    public function actionSortRank()
    {
        $modelExamHistory = new ExamHistory();
        $listExam = ExamHistory::find()->select(['exam_id'])->groupBy(['exam_id'])->indexBy('exam_id')->column();
        if (count($listExam) > 0) {
            $transaction = \yii::$app->getDb()->beginTransaction();
            foreach ($listExam as $key => $value) {
                $listUserForExam = ExamHistory::find()->select('exam_history_id')->where(['exam_id' => $value])->all();
                if (count($listUserForExam) > 0) {
                    foreach ($listUserForExam as $key1 => $value1) {
                        $detailExamHistory = ExamHistory::findOne(['exam_history_id' => $value1->exam_history_id]);
                        $rankExam = $modelExamHistory->getRankExam($value1->exam_history_id, $value);
                        $detailExamHistory->rank_exam = $rankExam[0]['rank'];
                        $detailExamHistory->save();
                    }
                }
            }
            $transaction->commit();
        }
    }
}