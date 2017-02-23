<?php

namespace backend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use backend\models\LoginForm;
use yii\filters\VerbFilter;
use common\models\Category;
use common\models\Answer;
use common\models\Quiz;
use common\models\QuizAnswer;
use yii\web\Response;
use yii\web\UploadedFile;
use yii\web\Session;

class QuestionController extends Controller {

    public function behaviors() {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['index', 'save', 'getsubcategory', 'detail'],
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
        $param = $request->queryParams;
        if (!empty($param['Quiz'])) {
            $formSearch->setAttributes($param['Quiz']);
        }
        $dataProvider = $formSearch->getData(1);
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
            'rootCat' => $rootCat
        ]);
    }
    
    /**
     * Detail question
     *
     * @date : 15-02-2017
     *
     */
    public function actionDetail($quizId) {
        $model = new Quiz();
        $quizItem = $model->find()->where(['quiz_id' => $quizId, 'type' => 1,'delete_flag' => 0])->one();
        if (empty($quizItem)) {
            return Yii::$app->response->redirect(['/error/error']);
        }
        
        return $this->render('detail', ['quizItem' => $quizItem]);
    }
    
    
    /*
     * Auth : 
     * 
     * Method :
     * Create : 09-02-2017
     */
    
    public function actionSave($quizId = null) {
        $session = Yii::$app->session;
        $request = Yii::$app->request;
        $rootCat = Category::find()->select('name')->where(['level' => 1])->indexBy('cateory_id')->column();
        $question = new Quiz();
        $answer = [];
        $quizAnswer = [];
        // create model
        for ($i =1 ; $i <= 8; $i++) {
            $keyAns = 'answer'.$i;
            $keyQuizAns = 'quiz_answer' .$i;
            $quizAnswer[$keyQuizAns] = new QuizAnswer();
            $answer[$keyAns] = new Answer();
        }
        $flag = 0;
        if (!empty($quizId)) {
            for ($i =1 ; $i <= 8; $i++) {
                $key = 'answer' .$i;
                $keyQuizAns = 'quiz_answer' .$i;
                $modelAnswer = Answer::findOne(['quiz_id' => $quizId, 'order' => $i]);
                $modelAnswer = ($modelAnswer) ? $modelAnswer : new Answer();
                $answer[$key] = $modelAnswer;
                $modelQuizAns = new QuizAnswer();
                
                if ($modelAnswer) {
                    $modelQuizAnsDetail = QuizAnswer::findOne(['quiz_id' => $quizId, 'answer_id' => $modelAnswer->answer_id]);
                    if ($modelQuizAnsDetail) {
                        $modelQuizAns = $modelQuizAnsDetail;
                        $modelQuizAns->quiz_ans_flg = 1;
                    }
                }
                $quizAnswer[$keyQuizAns] = $modelQuizAns;
            }
            $question = Quiz::find()->where(['quiz_id' => $quizId, 'type' => 1, 'delete_flag' => 0])->one();
            if (!$question) {
                return Yii::$app->response->redirect(['error/error']);
            }
            $flag = 1;
        }
        if ($request->isPost) {
            $dataPost = $request->Post();
            $question->addQuiz($dataPost, $answer, $quizAnswer, $flag);
        }
        return $this->render('save', [
            'rootCat' => $rootCat,
            'question' => $question,
            'answer' => $answer,
            'quizAnswer' => $quizAnswer,
            'flag' => $flag
        ]);
    }
    /*
     * Auth : 
     * 
     * Method :
     * Create : 12-02-2017
     */
    
    public function actionGetsubcategory() {
        $result = [];
        $request = Yii::$app->request;
        $id = $request->getQueryParam('id');
        $level = $request->getQueryParam('level');
        $subCat = Category::find()->select('name')->where(['parent_id' => $id])->indexBy('cateory_id')->column();
        $result['success'] = 1;
        $data = '<option value="">Select sub'. ($level-1) .' category</option>';
        if (count($subCat) > 0) {
            foreach ($subCat as $key => $value) {
                $data .= '<option value="'.$key.'">'.$value.'</option>';
            }
        }
        $result['data'] = $data;
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return $result;
    }
}
