<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use common\models\Member;

/**
 * This is the model class for table "follow".
 *
 * @property integer $follow_id
 * @property integer $member_id_followed
 * @property integer $member_id_following
 * @property integer $delete_flag
 * @property string $updated_date
 * @property string $created_date
 */
class Follow extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'follow';
    }

    const SCENARIO_FOLLOW = 'follow';
    
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
            [['member_id_followed', 'member_id_following'], 'required'],
            [['member_id_following'], 'validateFollowing', 'on' => self::SCENARIO_FOLLOW],
            [['member_id_followed', 'member_id_following', 'delete_flag'], 'integer'],
            [['updated_date', 'created_date'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'follow_id' => 'Follow ID',
            'member_id_followed' => 'Member Id Followed',
            'member_id_following' => 'Member Id Following',
            'delete_flag' => 'Delete Flag',
            'updated_date' => 'Updated Date',
            'created_date' => 'Created Date',
        ];
    }
    
    public function validateFollowing($attribute)
    {
        $member = Member::findOne(['member_id' => $this->member_id_following]);
        $follow = Follow::findOne(['member_id_followed' => $this->member_id_followed, 'member_id_following' => $this->member_id_following, 'delete_flag' => 0]);
        if ($follow || !$member || ($this->member_id_following == $this->member_id_followed)) {
            $this->addError($attribute, \Yii::t('app', 'existing',['attribute' => $this->attributeLabels()[$attribute]]));
        }
    }


    /*
     * Get list follow
     * 
     * Auth : 
     * Create : 28-02-2017
     */
    
    public function getListFollow($memberId, $limit, $offset){
        $query = new \yii\db\Query();
        $query->select(['member.*'])
                ->from('follow');
        $query->join('INNER JOIN', 'member', 'follow.member_id_following = member.member_id');
        $query->where(['=', 'follow.delete_flag', 0]);
        $query->andWhere(['=', 'follow.member_id_followed', $memberId]);
        $query->offset($offset);
        $query->limit($limit);
        return $query->all();
    }
    
    
    /*
     * Get list Following
     * 
     * Auth : 
     * Create : 28-02-2017
     */
    
    public function getListFollowing($memberId, $limit, $offset){
        $query = new \yii\db\Query();
        $query->select(['member.*'])
                ->from('follow');
        $query->join('INNER JOIN', 'member', 'follow.member_id_followed = member.member_id');
        $query->where(['=', 'follow.delete_flag', 0]);
        $query->andWhere(['=', 'follow.member_id_following', $memberId]);
        $query->offset($offset);
        $query->limit($limit);
        return $query->all();
    }
}
