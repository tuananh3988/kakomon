<?php

namespace common\models;

use Yii;

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

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['status', 'type_blood', 'sex', 'auth_key'], 'integer'],
            [['birthday', 'mail', 'password'], 'required'],
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
}
