<?php
return [
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'apns' => [
		'class' => 'bryglen\apnsgcm\Apns',
		'environment' => \bryglen\apnsgcm\Apns::ENVIRONMENT_SANDBOX,
		'pemFile' => dirname(__FILE__).'/APNS-Purelamo-dev.pem',
		// 'retryTimes' => 3,
		'options' => [
			'sendRetryTimes' => 5
		]
	],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
    ],
];
