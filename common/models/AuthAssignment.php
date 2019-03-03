<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%auth_assignment}}".
 *
 * @property string $item_name
 * @property int $user_id
 * @property int $created_at
 *
 * @property AuthItem $item
 * @property User $user
 */
class AuthAssignment extends \yii\db\ActiveRecord
{
    /**
     * Invalidate RBAC component's cache.
     */
    public static function invalidateCache()
    {
        /** @var \yii\rbac\DbManager $authManager */
        $authManager = Yii::$app->getAuthManager();
        $authManager->invalidateCache();
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%auth_assignment}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['item_name', 'user_id'], 'required'],
            [['user_id', 'created_at'], 'integer'],
            [['item_name'], 'string', 'max' => 64],
            [['item_name', 'user_id'], 'unique', 'targetAttribute' => ['item_name', 'user_id']],
            [['item_name'], 'exist', 'skipOnError' => true, 'targetClass' => AuthItem::class, 'targetAttribute' => ['item_name' => 'name']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'item_name' => Yii::t('app.model', 'Item Name'),
            'user_id' => Yii::t('app.model', 'User ID'),
            'created_at' => Yii::t('app.model', 'Created At'),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        self::invalidateCache();
    }

    /**
     * {@inheritdoc}
     */
    public function afterDelete()
    {
        parent::afterDelete();

        self::invalidateCache();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getItem()
    {
        return $this->hasOne(AuthItem::className(), ['name' => 'item_name']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}
