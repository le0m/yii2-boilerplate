<?php

namespace frontend\models;

use common\models\AuthItem;
use common\models\User;
use Yii;
use yii\base\Model;


/**
 * Signup form
 */
class SignupForm extends Model
{
    public $username;
    public $email;
    public $password;
    public $repeat;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['username', 'trim'],
            ['username', 'required'],
            ['username', 'unique', 'targetClass' => User::class, 'message' => Yii::t('app.validation', "This username has already been taken.")],
            ['username', 'string', 'min' => 2, 'max' => 190],

            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'string', 'max' => 190],
            ['email', 'unique', 'targetClass' => User::class, 'message' => Yii::t('app.validation', "This email address has already been taken.")],

            ['password', 'required'],
            ['password', 'string', 'min' => Yii::$app->params['user.passwordMinLength']],

            ['repeat', 'required'],
            ['repeat', 'string', 'min' => Yii::$app->params['user.passwordMinLength']],
            ['repeat', 'compare', 'compareAttribute' => 'password', 'skipOnEmpty' => false, 'message' => Yii::t('app.validation', 'Passwords must be equal.')],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'username' => Yii::t('app.model', "Username"),
            'email' => Yii::t('app.model', "Email"),
            'password' => Yii::t('app.model', "Password"),
            'repeat' => Yii::t('app.model', "Repeat password"),
        ];
    }

    /**
     * Signs user up.
     *
     * @return User|null the saved model or null if saving fails
     */
    public function signup()
    {
        if (!$this->validate()) {
            return null;
        }
        
        $user = new User();
        $user->username = $this->username;
        $user->email = $this->email;
        $user->status = User::STATUS_INACTIVE;
        $user->setPassword($this->password);
        $user->generateAuthKey();
        $user->generateAccountActivationToken();

        if (!$user->save()) {
            Yii::error(sprintf("\nError creating user:\n%s\n", print_r($user->getErrors(), true)), __METHOD__);

            return null;
        }

        // assign role
        if (User::find()->count() == 1) {
            Yii::info(sprintf("\nSigning up first user, assigning 'admin' role.\n"), __METHOD__);
            $user->setRole(AuthItem::ROLE_ADMIN);
        } else {
            $user->setRole(AuthItem::ROLE_USER);
        }
        
        return $user;
    }

    /**
     * Send the activation email to the user.
     *
     * @param User $user
     *
     * @return bool
     */
    public function sendActivationEmail($user)
    {
        return Yii::$app
            ->mailer
            ->compose('accountActivationToken', ['user' => $user])
            ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name . ' robot'])
            ->setTo($this->email)
            ->setSubject(Yii::t('app.email', "Account activation for {appName}", ['appName' => Yii::$app->name]))
            ->send();
    }
}
