<?php

namespace common\models;

use Yii;
use yii\base\InvalidConfigException;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "setting".
 *
 * @property int $id
 * @property string $name
 * @property string $value
 * @property string $description
 * @property integer $created_at
 * @property integer $updated_at
 */
class Setting extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%setting}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'value'], 'required'],
            [['description'], 'string'],
            [['name', 'value'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app.model', 'ID'),
            'name' => Yii::t('app.model', 'Name'),
            'value' => Yii::t('app.model', 'Value'),
            'description' => Yii::t('app.model', 'Description'),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::class
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        $this->refreshCache();
    }

    /**
     * {@inheritdoc}
     */
    public function afterDelete()
    {
        parent::afterDelete();

        $this->refreshCache();
    }

    /**
     * Refresh component cache.
     *
     * @return bool
     */
    protected function refreshCache()
    {
        try {
            /** @var \common\components\Settings $settings */
            $settings = Yii::$app->get('settings');

            return $settings->refreshCache();
        } catch (InvalidConfigException $e) {
            Yii::error(sprintf("Invalid config exception for Settings component: %s", $e->getMessage()), __METHOD__);

            return false;
        }
    }
}
