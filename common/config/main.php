<?php
return [
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'apns' => [
		'class' => 'bryglen\apnsgcm\Apns',
		'environment' => \bryglen\apnsgcm\Apns::ENVIRONMENT_SANDBOX,
		'pemFile' => dirname(__FILE__).'/apnssert/cer.pem',
		// 'retryTimes' => 3,
		'options' => [
			'sendRetryTimes' => 5
		]
	],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning', 'info'],
                    'categories' => ['push'],
                    'logFile' => '@app/runtime/logs/push.log',
                ],
            ]
        ]
    ],
];
