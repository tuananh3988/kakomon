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
                'activity/comment/add' => 'activity/add-comment',
                'activity/comment/delete' => 'activity/delete-comment',
                'activity/comment/list' => 'activity/list-comment',
                'activity/help/add' => 'activity/add-help',
                'activity/help/delete' => 'activity/delete-help',
                'activity/help/list' => 'activity/list-help',
                'activity/help/timeline' => 'activity/timeline',
                'activity/reply/add' => 'activity/add-reply',
                'activity/reply/delete' => 'activity/delete-reply',
                'activity/reply/list' => 'activity/list-reply',
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
