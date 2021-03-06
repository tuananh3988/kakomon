<?php
return [
    'adminEmail' => 'admin@example.com',
    'supportEmail' => 'support@example.com',
    'user.passwordResetTokenExpire' => 3600,
    
    'aosApikey' => 'AIzaSyAX-0NOcdzcHUuVlOrKkXOe-xLodbqYNw8',
    'domainImg' => 'http://purelamo.com/wp-content/uploads/',
    'domain' => 'http://purelamo.com/',
    'pageSize' => 10,
    'imgPath' => '@backend/web/',
    'imgUpload' => [
        'question' => 'uploads/question/',
        'answer' => 'uploads/answer/',
        'member' => 'uploads/member/',
    ],
    'csvUpload' => [
        'process' => 'csvUpload/process/',
        'done' => 'csvUpload/done/'
    ],
    'limit' => [
        'sumary' => 20,
        'quiz' => 10,
        'follow' => 10,
        'timeline' => 10,
        'comment' => 4,
        'reply' => 4,
        'help' => 4,
        'notification' => 4
    ],
    'offset' => [
        'sumary' => 0,
        'quiz' => 0,
        'follow' => 0,
        'timeline' => 0,
        'comment' => 0,
        'reply' => 0,
        'help' => 0,
        'notification' => 0
    ]
];
