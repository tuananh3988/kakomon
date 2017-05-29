<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\data\ActiveDataProvider;
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
    const TYPE_COLLECT_QUIZ = 6;
    
    public static $NOTIFICATION_TYPE = [
        1 => 'LIKE',
        2 => 'REPLY',
        3 => 'FOLLOW',
        4 => 'QUICK QUIZ',
        5 => 'EXAM',
        6 => 'COLLECT QUIZ'
    ];
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
                            'member_name' => $activity['name'],
                            'total_like' => $activity['total'],
                            //'title' => $activity['name'] . 'さんから「いいね！」GET!' . $activity['total'],
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
                            'member_name' => $reply['name'],
                            //'title' => $reply['name'] . 'さんから「いいね！」GET!',
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
                            'member_name' => $follow['name'],
                            //'title' => $follow['name'] . 'さんからフォローされました。',
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
                            'exam_id' => (int)$value['related_id'],
                            'activity_id' => null,
                            'member_id' => null,
                            'avatar' => null,
                            'quiz_id' => null,
                            'content' => $exam['name'],
                            'start_date' => $exam['start_date'],
                            'title' => $exam['name'] . 'これを逃すと、もうできない！ ',
                            'created_date' => $value['created_date']
                        ];
                        break;
                    case 6:
                        $exam = Exam::getInforNotification($value['related_id']);
                        $listData[] = [
                            'type' => (int)$value['type'],
                            'exam_id' => (int)$value['related_id'],
                            'activity_id' => null,
                            'member_id' => null,
                            'avatar' => null,
                            'quiz_id' => null,
                            'status' => (int)$exam['status'],
                            'content' => $exam['exam_desc'],
                            'start_date' => $exam['start_date'],
                            'end_date' => $exam['end_date'],
                            'title' => '',
                            'created_date' => $value['created_date']
                        ];
                        break;
                    default :
                }
            }
        }
        return $listData;
    }
    
    
    /**
     * get list user
     * @Date 19-02-2017 
     */
    public function getData() {
        $query = new \yii\db\Query();
        $query->select(['notification.*'])
                ->from('notification');
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => Yii::$app->params['pageSize']
            ],
            'sort' => [
                'defaultOrder' => [
                    'notification_id' => SORT_DESC,
                    'created_date' => SORT_DESC
                ]
            ],
        ]);
        $dataProvider->sort->attributes['notification_id'] = [
            'desc' => ['notification.notification_id' => SORT_DESC],
            'asc' => ['notification.notification_id' => SORT_ASC],
        ];
        $dataProvider->sort->attributes['created_date'] = [
            'desc' => ['notification.created_date' => SORT_DESC],
            'asc' => ['notification.created_date' => SORT_ASC],
        ];
        return $dataProvider;
    }
    
    public static function getInfoNotification($type, $relatedId){
        $content = '';
        $type = (int)$type;
        switch ($type) {
            case 1:
                $activity = Activity::getInforNotification($relatedId);
                $content = $activity['content'];
                break;
            case 2:
                $reply = Reply::getInforNotification($relatedId);
                $content = $reply['content'];
                break;
            case 3:
                $follow = Follow::getInforNotification($relatedId);
                $content = $follow['name'] . 'さんからフォローされました。';
                break;
            case 4:
                $quiz = Quiz::getInforNotification($relatedId);
                $content = $quiz['question'];
                break;
            case 5:
                $exam = Exam::getInforNotification($relatedId);
                $content = $exam['name'];
                break;
            case 6:
                break;
            default :
        }
        return $content;
    }
}
