<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "member".
 *
 * @property integer $user_id
 * @property integer $status
 * @property string $city
 * @property string $job
 * @property integer $type_blood
 * @property string $favorite_animal
 * @property string $favorite_film
 * @property string $birthday
 * @property integer $sex
 * @property string $name
 * @property string $furigana
 * @property string $mail
 * @property string $password
 * @property string $nickname
 * @property integer $auth_key
 * @property string $created_date
 * @property string $updated_date
 */
class Member extends \yii\db\ActiveRecord implements \yii\web\IdentityInterface
{
    const STATUS_ACTIVE = 1;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'member';
    }

    const SCENARIO_SAVE = 'save';
    const SCENARIO_LOGIN = 'login';
    
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
            [['status', 'type_blood', 'sex'], 'integer'],
            [['birthday', 'mail', 'sex'], 'required'],
            [['birthday'], 'date', 'format' => 'php:Y-m-d'],
            ['mail', 'email'],
            [['mail'], 'validateUniqueMail', 'on' => self::SCENARIO_SAVE],
            [['password'], 'required', 'on' => self::SCENARIO_SAVE],
            [['password'], 'string', 'min' => 8],
            [['birthday', 'created_date', 'updated_date'], 'safe'],
            [['city', 'job', 'favorite_animal', 'favorite_film', 'name', 'furigana', 'mail', 'password', 'nickname'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'member_id' => 'Member ID',
            'status' => 'Status',
            'city' => 'City',
            'job' => 'Job',
            'type_blood' => 'Type Blood',
            'favorite_animal' => 'Favorite Animal',
            'favorite_film' => 'Favorite Film',
            'birthday' => 'Birthday',
            'sex' => 'Sex',
            'name' => 'Name',
            'furigana' => 'Furigana',
            'mail' => 'Mail',
            'password' => 'Password',
            'nickname' => 'Nickname',
            'auth_key' => 'Auth Key',
            'created_date' => 'Created Date',
            'updated_date' => 'Updated Date',
        ];
    }
    
    /*
     * validate unique mail
     * 
     * Auth : 
     * Create : 03-01-2017
     */
    public function validateUniqueMail($attribute)
    {
        if (!$this->hasErrors()) {
            $memberDetail = Member::findOne(['mail' => $this->$attribute]);
            if ($memberDetail) {
                $this->addError($attribute, 'Email already exists');
            }
        }
    }
    
    public static function findIdentity($id)
    {
        return static::findOne(['member_id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {

        return static::findOne(['auth_key' => $token]);
    }
    
    public function getId()
    {
        return $this->member_id;
    }
    public function getAuthKey()
    {
        return $this->auth_key;
    }
    public function validateAuthKey($authKey)
    {
        return $this->auth_key === $authKey;
    }
    
    /*
     * Find email
     * 
     * Auth : 
     * Create : 25-02-2017
     */
    public static function findEmail($email){
        $emailMember = Member::findOne(['mail' => $email]);
        if (!$emailMember) {
            return false;
        }
        return true;
    }
}
