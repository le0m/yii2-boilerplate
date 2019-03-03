<?php
namespace frontend\models;

use common\models\User;
use Yii;
use yii\base\InvalidArgumentException;
use yii\base\Model;

/**
 * Password reset form
 */
class ResetPasswordForm extends Model
{
    public $password;
    public $repeat;


    /**
     * @var \common\models\User
     */
    private $_user;


    /**
     * Creates a form model given a token.
     *
     * @param string $token
     * @param array $config name-value pairs that will be used to initialize the object properties
     *
     * @throws \yii\base\InvalidParamException if token is empty or not valid
     */
    public function __construct($token, $config = [])
    {
        if (empty($token) || !is_string($token)) {
            throw new InvalidArgumentException('Password reset token cannot be blank.');
        }

        $this->_user = User::findByPasswordResetToken($token);

        if (!$this->_user) {
            throw new InvalidArgumentException('Wrong password reset token.');
        }

        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
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
            'password' => Yii::t('app.model', "Password"),
            'repeat' => Yii::t('app.model', "Repeat password"),
        ];
    }

    /**
     * Resets password.
     *
     * @return bool if password was reset.
     */
    public function resetPassword()
    {
        $user = $this->_user;
        $user->setPassword($this->password);
        $user->removePasswordResetToken();

        if (!$user->save()) {
            Yii::error(sprintf("\nError resetting password of account %d:\n%s\n", $user->id, print_r($user->getErrors(), true)), __METHOD__);

            return false;
        }

        return true;
    }
}
