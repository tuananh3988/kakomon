<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-frontend',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'frontend\controllers',
    'language' => 'en',
    'components' => [
        'user' => [
            'identityClass' => 'common\models\Member',
            'enableSession' => false,
            'loginUrl' => null,
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
                'member/info/<id:\d+>' => 'member/info',
                'activity/comment/add' => 'activity/addcomment',
                'activity/comment/delete' => 'activity/deletecomment',
            ],
        ],
        'request' => [
            'baseUrl' => '/api',
            'enableCsrfValidation'=> FALSE
        ],
        'response' => [
            'format' => yii\web\Response::FORMAT_JSON,
        ]

    ],
    'params' => $params,
];
