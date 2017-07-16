<?php

namespace common\components;

use Yii;
use yii\base\Component;
use common\models\Notification;

class PushNotification extends Component
{
    /*
     * push notification
     * 
     * Auth : 
     * Created : 15-07-2017
     */
    
    public static function pushNotification($type, $relatedId, $nameMember = NULL, $memberId = NULL, $message = NULL) {
        Yii::info('Notification push: start', 'push');
        $apns = Yii::$app->apns;
        $dataPush = Notification::renderTitleNotificationAndListMemeber($type, $relatedId, $nameMember, $memberId, $message);
        if (count($dataPush['divice']) > 0) {
            $mgs = $apns->sendMulti($dataPush['divice'], $dataPush['title'],
                [
                  'customProperty' => 'Hello',
                ],
                [
                  'sound' => 'default',
                  'badge' => 1
                ]
            );
            Yii::info('log ios: ', 'push');
            Yii::info($mgs, 'push');
        }
        Yii::info('Notification push: End', 'push');
        return true;
    }
}
