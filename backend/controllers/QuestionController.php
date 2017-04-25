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
use backend\models\FormImportCSV;
use common\components\Utility;
use common\models\LogCsv;

class QuestionController extends Controller {

    public function behaviors() {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['index', 'save', 'getsubcategory', 'detail', 'import'],
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
        $year = Quiz::find()->select('quiz_year')->where(['delete_flag' => Quiz::QUIZ_ACTIVE, 'type' => Quiz::TYPE_NORMAL])->orderBy(['quiz_year' => SORT_DESC])->distinct()->indexBy('quiz_year')->column();
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
            'rootCat' => $rootCat,
            'year' => $year
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
        
        // create model
        for ($i =1 ; $i <= 8; $i++) {
            $keyAns = 'answer'.$i;
            $answer[$keyAns] = new Answer();
        }
        $flag = 0;
        if (!empty($quizId)) {
            for ($i =1 ; $i <= 8; $i++) {
                $keyAns = 'answer' .$i;
                $modelAnswer = Answer::findOne(['quiz_id' => $quizId, 'order' => $i]);
                $answer[$keyAns] = ($modelAnswer) ? $modelAnswer : new Answer();
            }
            $question = Quiz::find()->where(['quiz_id' => $quizId, 'type' => 1, 'delete_flag' => 0])->one();
            
            if (!$question) {
                return Yii::$app->response->redirect(['error/error']);
            }
            
            //set value for quiz_answer
            $listAnswer = str_split($question->quiz_answer);
            for ($i = 0; $i < count($listAnswer); $i++) {
                $key = 'quiz_answer'.($i+1);
                $question->$key = $listAnswer[$i];
            }
            $flag = 1;
        }
        if ($request->isPost) {
            $dataPost = $request->Post();
            $question->addQuiz($dataPost, $answer, $flag);
        }
        
        return $this->render('save', [
            'rootCat' => $rootCat,
            'question' => $question,
            'answer' => $answer,
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
    
    /*
     * Auth : 
     * 
     * Method :
     * Create : 03-03-2017
     */
    
    public function actionImport()
    {
        $time = strtotime(date('Y-m-d H:i:s'));
        $session = Yii::$app->session;
        $model = new FormImportCSV();
        $line = 0;
        if ($model->load(Yii::$app->request->post())) {
            $model->file = UploadedFile::getInstance($model, 'file');
            $model->file_images = UploadedFile::getInstance($model, 'file_images');
            if ($model->validate()) {
                $model->file->name = $time.'.csv';
                Utility::uploadCsv($model->file, 'process', $model->file->name);
                if ($model->file_images) {
                    $model->file_images->name = $time.'.tar';
                    Utility::uploadCsv($model->file_images, 'process', $model->file_images->name);
                }
                //inset table log
                $modelLogCsv = new LogCsv();
                $modelLogCsv->file_name = (string)$time;
                $modelLogCsv->save();
                //set message after upload file csv and images
                $session = Yii::$app->session;
                $message ='Csv file is being inserted, please wait for a few minutes. You can switch to the upload status monitor.';
                $session->setFlash('sucess_csv',$message);
            }
        }
        
        return $this->render('import', [
            'model' => $model
        ]);
    }
}
