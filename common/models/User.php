<?php
namespace common\models;

use common\components\exceptions\FailedLoginsException;
use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * User model
 *
 * @property integer $id
 * @property string $username
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $account_activation_token
 * @property integer $failed_logins
 * @property integer $last_failed_login
 * @property string $email
 * @property string $auth_key
 * @property string $language
 * @property string $timezone
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property string $password write-only password
 */
class User extends ActiveRecord implements IdentityInterface
{
    const STATUS_DELETED = 0;
    const STATUS_INACTIVE = 5;
    const STATUS_ACTIVE = 10;


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['status', 'default', 'value' => self::STATUS_INACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_INACTIVE, self::STATUS_DELETED]],
            ['failed_logins', 'default', 'value' => 0],

            [['username', 'auth_key', 'password_hash', 'email', 'status', 'failed_logins'], 'required'],
            [['username', 'password_hash', 'password_reset_token', 'account_activation_token', 'email', 'timezone'], 'string', 'max' => 190],
            [['auth_key'], 'string', 'max' => 32],
            [['language'], 'string', 'max' => 45],
            [['failed_logins', 'last_failed_login'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app.model', 'ID'),
            'username' => Yii::t('app.model', 'Username'),
            'email' => Yii::t('app.model', 'Email'),
            'password_hash' => Yii::t('app.model', 'Password Hash'),
            'auth_key' => Yii::t('app.model', 'Auth Key'),
            'password_reset_token' => Yii::t('app.model', 'Password Reset Token'),
            'account_activation_token' => Yii::t('app.model', 'Account Activation Token'),
            'failed_logins' => Yii::t('app.model', 'Failed Logins'),
            'last_failed_login' => Yii::t('app.model', 'Last Failed Login'),
            'language' => Yii::t('app.model', 'Language'),
            'status' => Yii::t('app.model', 'Status'),
            'timezone' => Yii::t('app.model', 'Timezone'),
            'created_at' => Yii::t('app.model', 'Created At'),
            'updated_at' => Yii::t('app.model', 'Updated At'),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['auth_key' => $token]);
    }

    /**
     * Finds user by account activation token.
     *
     * @param string $token
     *
     * @return User|null
     */
    public static function findByAccountActivationToken($token)
    {
        return static::findOne([
            'account_activation_token' => $token,
            'status' => self::STATUS_INACTIVE
        ]);
    }

    /**
     * Finds user by username
     *
     * @param string $username
     *
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by email.
     *
     * @param string $email
     *
     * @return static|null
     */
    public static function findByEmail($email)
    {
        return static::findOne(['email' => $email, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     *
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     *
     * @return bool
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];

        return $timestamp + $expire >= time();
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password.
     *
     * If the user fails too many logins, the login is prevented
     * for a period of time.
     * The delay is reset each subsequent failed attempt.
     *
     * @param string $password password to validate
     *
     * @return bool if password provided is valid for current user
     *
     * @throws \common\components\exceptions\FailedLoginsException if the user failed too many logins
     */
    public function validatePassword($password)
    {
        if ($this->failed_logins >= Yii::$app->params['user.maxFailedLogins']) {
            if (time() < ($this->last_failed_login + Yii::$app->params['user.failedLoginsDelay'])) {
                throw new FailedLoginsException('User failed too many logins');
            }
        }

        if (Yii::$app->security->validatePassword($password, $this->password_hash)) {
            $this->failed_logins = 0;
            $this->last_failed_login = null;
            $this->save(false, ['failed_logins', 'last_failed_login']);

            return true;
        }

        $this->failed_logins++;
        $this->last_failed_login = time();
        $this->save(false, ['failed_logins', 'last_failed_login']);

        return false;
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Generates new account activation token
     */
    public function generateAccountActivationToken()
    {
        $this->account_activation_token = Yii::$app->security->generateRandomString();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

    /**
     * Removes account activation token
     */
    public function removeAccountActivationToken()
    {
        $this->account_activation_token = null;
    }

    /**
     * Sets user role.
     *
     * @param \yii\rbac\Role|string $role
     *
     * @return boolean success of operation
     */
    public function setRole($role)
    {
        $auth = Yii::$app->authManager;

        if (is_string($role)) {
            $strRole = $role;
            $role = $auth->getRole($role);

            if ($role === null) {
                Yii::error(sprintf("\nRole '%s' not found, was assigning to user %d", $strRole, $this->id), __METHOD__);
                return false;
            }
        }

        try {
            $auth->assign($role, $this->id);
            AuthAssignment::invalidateCache();
        }
        catch (\Exception $ex) {
            Yii::error(sprintf("\nError assigning role '%s' to user %d:\n%s\n", $role->name, $this->id, $ex->getMessage()), __METHOD__);
            return false;
        }

        return true;
    }
}
