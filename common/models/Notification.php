<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use common\models\Activity;
use common\models\Follow;
use common\models\Quiz;
use common\models\Exam;
use frontend\models\Reply;
use common\components\Utility;
/**
 * This is the model class for table "notification".
 *
 * @property integer $notification_id
 * @property integer $type
 * @property string $title
 * @property integer $activity_id
 * @property integer $member_id
 * @property string $created_date
 * @property string $updated_date
 */
class Notification extends \yii\db\ActiveRecord
{
    
    const TYPE_LIKE = 1;
    const TYPE_REPLY = 2;
    const TYPE_FOLLOW = 3;
    const TYPE_QUICK_QUIZ = 4;
    const TYPE_EXAM = 5;
    const TYPE_COLLECT_QUIZ = 5;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'notification';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type', 'related_id', 'member_id'], 'integer'],
            [['created_date', 'updated_date'], 'safe'],
            [['title'], 'string', 'max' => 255],
        ];
    }
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                          ActiveRecord::EVENT_BEFORE_INSERT => ['created_date'],
                          ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_date'],
                ],
                'value' => date('Y-m-d H:i:s'),
            ],
        ];
    }
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'notification_id' => 'Notification ID',
            'type' => 'Type',
            'title' => 'Title',
            'activity_id' => 'Activity ID',
            'member_id' => 'Member ID',
            'created_date' => 'Created Date',
            'updated_date' => 'Updated Date',
        ];
    }
    
    /*
     * Get List comment
     * 
     * Auth : 
     * Creat : 02-03-2017
     */
    
    public static function getListNotificationByMember($limit, $offset, $flag = false)
    {
        $query1 = (new \yii\db\Query())
            ->select('notification.*')
            ->from('notification')
            ->where(['=', 'member_id', Yii::$app->user->identity->member_id]);

        $query2 = (new \yii\db\Query())
            ->select("notification.*")
            ->from('notification')
            ->where(['IS', 'member_id', NULL]);

        $query1->union($query2, false);
        $sql = $query1->createCommand()->getRawSql();
        $sql .= ' ORDER BY notification_id DESC';
        if (!$flag) {
            $sql .= ' LIMIT '. $limit . ' OFFSET ' . $offset;
        }
        $query = Notification::findBySql($sql);
        if ($flag) {
            return $query->count();
        }
        return $query->all();
    }
    
    /*
     * Render List Comment
     * 
     * Auth : 
     * Create : 02-03-2017
     */
    
    public static function renderListNotification($limit, $offset)
    {
        $listData = [];
        $list = self::getListNotificationByMember($limit, $offset);
        if (count($list) > 0){
            foreach ($list as $key => $value) {
                $type = (int)$value['type'];
                switch ($type) {
                    case 1:
                        $activity = Activity::getInforNotification($value['related_id']);
                        $listData[] = [
                            'type' => (int)$value['type'],
                            'activity_id' => (int)$activity['activity_id'],
                            'member_id' => (int)$activity['member_id'],
                            'avatar' => Utility::getImage('member', $activity['member_id'], null, true),
                            'quiz_id' => (int)$activity['quiz_id'],
                            'content' => $activity['content'],
                            'title' => $activity['name'] . 'さんから「いいね！」GET!' . $activity['total'],
                            'created_date' => $value['created_date']
                        ];
                        break;
                    case 2:
                        $reply = Reply::getInforNotification($value['related_id']);
                        $listData[] = [
                            'type' => (int)$value['type'],
                            'activity_id' => (int)$reply['activity_id'],
                            'member_id' => (int)$reply['member_id'],
                            'avatar' => Utility::getImage('member', $reply['member_id'], null, true),
                            'quiz_id' => (int)$reply['quiz_id'],
                            'content' => $reply['content'],
                            'title' => $reply['name'] . 'さんから「いいね！」GET!',
                            'created_date' => $value['created_date']
                        ];
                        break;
                    case 3:
                        $follow = Follow::getInforNotification($value['related_id']);
                        $listData[] = [
                            'type' => (int)$value['type'],
                            'activity_id' => null,
                            'member_id' => (int)$follow['member_id'],
                            'avatar' => Utility::getImage('member', $follow['member_id'], null, true),
                            'quiz_id' => null,
                            'content' => null,
                            'title' => $follow['name'] . 'さんからフォローされました。',
                            'created_date' => $value['created_date']
                        ];
                        break;
                    case 4:
                        $quiz = Quiz::getInforNotification($value['related_id']);
                        $listData[] = [
                            'type' => (int)$value['type'],
                            'activity_id' => null,
                            'member_id' => null,
                            'avatar' => null,
                            'quiz_id' => $quiz['quiz_id'],
                            'content' => $quiz['question'],
                            'title' => $quiz['question'],
                            'created_date' => $value['created_date']
                        ];
                        break;
                    case 5:
                        $exam = Exam::getInforNotification($value['related_id']);
                        $listData[] = [
                            'type' => (int)$value['type'],
                            'activity_id' => null,
                            'member_id' => null,
                            'avatar' => null,
                            'quiz_id' => null,
                            'content' => $exam['name'],
                            'title' => date("Y-m-d H:i", strtotime($exam['start_date'])) . ' ' . $exam['name'] . '<br/>これを逃すと、もうできない！ ',
                            'created_date' => $value['created_date']
                        ];
                        break;
                    case 6:
                        break;
                    default :
                }
            }
        }
        return $listData;
    }
}
