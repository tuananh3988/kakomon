<?php
namespace frontend\models;

use Yii;
use yii\base\Model;
use common\models\Member;
/**
 * Login form
 */
class LoginForm extends Model
{
    public $mail;
    public $password;
    public $device_id;
    public $device_token;

    private $_user;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['mail', 'password', 'device_id' , 'device_token'], 'required'],
            ['mail', 'email'],
            // password is validated by validatePassword()
            ['password', 'validatePassword'],
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'mail' => 'Mail',
            'password' => 'Password',
            'device_token' => 'Device Token',
            'device_id' => 'Device Id'
        ];
    }
    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user || !Yii::$app->security->validatePassword($this->password, $user->password)) {
                $this->addError($attribute, 'Incorrect username or password.');
            }
        }
    }
    
    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    protected function getUser()
    {
        if ($this->_user === null) {
            $this->_user = Member::findOne(['mail' => $this->mail]);
        }

        return $this->_user;
    }
}
