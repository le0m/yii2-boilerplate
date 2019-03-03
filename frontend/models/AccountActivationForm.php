<?php

namespace frontend\models;

use common\models\User;
use Yii;
use yii\base\InvalidArgumentException;
use yii\base\Model;


class AccountActivationForm extends Model
{
    /**
     * @var \common\models\User
     */
    private $_user;


    /**
     * Get the user model given a token.
     *
     * @param string $token
     * @param array $config name-value pairs that will be used to initialize the object properties
     *
     * @throws \yii\base\InvalidParamException if token is empty or not valid
     */
    public function __construct($token, $config = [])
    {
        if (empty($token) || !is_string($token)) {
            throw new InvalidArgumentException('Account activation token cannot be blank.');
        }

        $this->_user = User::findByAccountActivationToken($token);

        if (!$this->_user) {
            throw new InvalidArgumentException('Wrong account activation token.');
        }

        parent::__construct($config);
    }

    /**
     * Activates the account.
     *
     * @return bool whether the account was activated
     */
    public function activateAccount()
    {
        $user = $this->_user;
        $user->status = User::STATUS_ACTIVE;
        $user->removeAccountActivationToken();

        if (!$user->save()) {
            Yii::error(sprintf("\nError activating account %d:\n%s\n", $user->id, print_r($user->getErrors(), true)), __METHOD__);

            return false;
        }

        return true;
    }
}