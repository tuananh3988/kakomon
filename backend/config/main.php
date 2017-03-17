<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-backend',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',
    'defaultRoute' => 'site',
    'bootstrap' => ['log'],
    'layout' => 'gentellela',
    'modules' => [
        'treemanager' =>  [
            'class' => '\kartik\tree\Module',
            // other module settings, refer detailed documentation
        ]
    ],
    'language' => 'en',
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-backend',
        ],
        'user' => [
            'identityClass' => 'common\models\Staffs',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-backend', 'httpOnly' => true],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the backend
            'name' => 'advanced-backend',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                'login' => 'site/login',
                'logout' => 'site/logout',
                'category/detail/<id:\w+>' => 'category/detail',
                'category/checkdelete/<id:\w+>' => 'category/checkdelete',
                'question/detail/<quizId:\w+>' => 'question/detail',
                'question/edit/<quizId:\w+>' => 'question/save',
                'question/getsubcategory/<id:\w+>/<level:\w+>' => 'question/getsubcategory',
                'collect/detail/<quizId:\w+>' => 'collect/detail',
                'exam/detail/<examId:\w+>' => 'exam/detail',
                'exam/edit/<examId:\w+>' => 'exam/save',
                'quick/edit/<quizId:\w+>' => 'quick/save',
                'quick/detail/<quizId:\w+>' => 'quick/detail',
                'csv/detail/<logId:\w+>' => 'csv/detail',
            ],
        ],
        'urlManagerBackend' => [
                'class' => 'yii\web\urlManager',
                'baseUrl' => 'backend/web/',
                'enablePrettyUrl' => true,
                'showScriptName' => false,
        ],
        'request' => [
            'baseUrl' => '/backend',
            'enableCookieValidation' => true,
            'enableCsrfValidation' => true,
            'cookieValidationKey' => 'xxxxxxx',
        ],
        
    ],
    'as beforeAction' => [
        'class' => 'backend\components\setCategory'
    ],
    'params' => $params,
];
