<?php

namespace backend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use common\models\Category;
use common\models\Answer;
use common\models\Quiz;
use yii\web\Session;
use common\models\Exam;
use common\models\ExamQuiz;
use common\models\Collect;

class CollectController extends Controller {

    public function behaviors() {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['index', 'detail'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                //'logout' => ['post'],
                ],
            ],
        ];
    }

    public function actionIndex() {
        $request = Yii::$app->request;
        $formSearch = new Quiz();
        $rootCat = Category::find()->select('name')->where(['level' => 1])->indexBy('cateory_id')->column();
        $collect = Collect::find()->select('collect_name')->indexBy('collect_id')->column();
        $year = Quiz::find()->select('quiz_year')->where(['delete_flag' => Quiz::QUIZ_ACTIVE, 'type' => Quiz::TYPE_COLLECT])->andWhere(['not', ['quiz_year' => null]])->orderBy(['quiz_year' => SORT_DESC])->distinct()->indexBy('quiz_year')->column();
        $param = $request->queryParams;
        if (!empty($param['Quiz'])) {
            $formSearch->setAttributes($param['Quiz']);
        }
        $dataProvider = $formSearch->getData(3);
        if ($request->isPost) {
            $post = $request->post();
            if ($post['idQuestion']){
                $questionDelete = Quiz::findOne(['quiz_id' => $post['idQuestion']]);
                $message = '';
                if ($questionDelete) {
                    $questionDelete->delete_flag = 1;
                    $questionDelete->save();
                    $message = 'You just delete a question!';
                    Yii::$app->session->setFlash('message_delete', $message);
                }
            }
        }
        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'formSearch' => $formSearch,
            'rootCat' => $rootCat,
            'year' => $year,
            'collect' => $collect
        ]);
    }
    
    /**
     * Detail question
     *
     * @date : 15-02-2017
     *
     */
    public function actionDetail($quizId) {
        $session = Yii::$app->session;
        $request = Yii::$app->request;
        $modelExamQuiz = new ExamQuiz();
        $model = new Quiz();
        $quizItem = $model->find()->where(['quiz_id' => $quizId, 'delete_flag' => 0])->one();
        $listExam = Exam::renderListExam($quizId);
        
        if (empty($quizItem)) {
            return Yii::$app->response->redirect(['/error/error']);
        }
        if ($request->isPost) {
            $dataPost = $request->Post();
            $modelExamQuiz->load($dataPost);
            $modelExamQuiz->quiz_id = $quizId;
            if ($modelExamQuiz->validate()) {
                $modelExamQuiz->save();
                $message = 'You have successfully add to exam questions!';
                Yii::$app->session->setFlash('sucess_exam',$message);
                return Yii::$app->response->redirect(['/exam/detail', 'examId' => $modelExamQuiz->exam_id]);
            }
        }
        return $this->render('detail', [
            'quizItem' => $quizItem,
            'listExam' => $listExam,
            'modelExamQuiz' => $modelExamQuiz
        ]);
    }
    
}
