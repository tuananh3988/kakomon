<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\data\ActiveDataProvider;
/**
 * This is the model class for table "log_csv".
 *
 * @property integer $log_id
 * @property integer $status
 * @property string $file_name
 * @property string $content_log
 * @property string $created_date
 * @property string $updated_date
 */
class LogCsv extends \yii\db\ActiveRecord
{
    const STATUS_WAIT = 1;
    const STATUS_PROCESS = 2;
    const STATUS_DONE = 3;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'log_csv';
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
    public function rules()
    {
        return [
            [['status'], 'integer'],
            [['file_name'], 'required'],
            [['content_log'], 'string'],
            [['created_date', 'updated_date'], 'safe'],
            [['file_name'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'log_id' => 'Log ID',
            'status' => 'Status',
            'file_name' => 'File Name',
            'content_log' => 'Content Log',
            'created_date' => 'Created Date',
            'updated_date' => 'Updated Date',
        ];
    }
    
    /**
     * get list Log csv
     * @Date 14-03-2017 
     */
    public function getData() {
        $query = new \yii\db\Query();
        $query->select(['log_csv.*'])
                ->from('log_csv');
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => Yii::$app->params['pageSize']
            ],
            'sort' => [
                'defaultOrder' => [
                    'log_id' => SORT_DESC,
                    'created_date' => SORT_DESC,
                    'updated_date' => SORT_DESC
                ]
            ],
        ]);
        $dataProvider->sort->attributes['log_id'] = [
            'desc' => ['log_csv.log_id' => SORT_DESC],
            'asc' => ['log_csv.log_id' => SORT_ASC],
        ];
        $dataProvider->sort->attributes['created_date'] = [
            'desc' => ['log_csv.created_date' => SORT_DESC],
            'asc' => ['log_csv.created_date' => SORT_ASC],
        ];
        $dataProvider->sort->attributes['updated_date'] = [
            'desc' => ['log_csv.updated_date' => SORT_DESC],
            'asc' => ['log_csv.updated_date' => SORT_ASC],
        ];
        return $dataProvider;
    }
}
