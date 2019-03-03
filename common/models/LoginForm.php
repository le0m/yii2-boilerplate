<?php

namespace common\models;

use common\components\exceptions\FailedLoginsException;
use Yii;
use yii\base\Model;

/**
 * Login form
 */
class LoginForm extends Model
{
    public $email;
    public $password;
    public $rememberMe = true;

    private $_user;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['email', 'password'], 'required'],
            ['rememberMe', 'boolean'],

            ['password', 'validatePassword'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'email' => Yii::t('app.model', "Email"),
            'password' => Yii::t('app.model', "Password"),
            'rememberMe' => Yii::t('app.model', "Remember me"),
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     */
    public function validatePassword($attribute)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();

            try {
                if (!$user || !$user->validatePassword($this->password)) {
                    $this->addError($attribute, Yii::t('app.validation', "Incorrect email or password."));
                }
            } catch (FailedLoginsException $e) {
                $this->addError('email', Yii::t('app.validation', "You tried too many wrong combinations. Wait {minutes} before trying again.", ['minutes' => (Yii::$app->params['user.failedLoginsDelay'] / 60)]));
            }
        }
    }

    /**
     * Logs in a user using the provided username and password.
     *
     * @return bool whether the user is logged in successfully
     */
    public function login()
    {
        if ($this->validate()) {
            $duration = $this->rememberMe ? Yii::$app->params['user.rememberMeDuration'] : 0;

            return Yii::$app->user->login($this->getUser(), $duration);
        }

        return false;
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    protected function getUser()
    {
        if ($this->_user === null) {
            $this->_user = User::findByEmail($this->email);
        }

        return $this->_user;
    }
}
