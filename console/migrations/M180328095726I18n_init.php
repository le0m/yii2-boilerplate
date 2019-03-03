<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace console\migrations;

use Yii;
use yii\base\InvalidConfigException;
use yii\db\Migration;
use yii\i18n\I18N;

/**
 * Initialize i18n tables.
 *
 * @author Dmitry Naumenko <d.naumenko.a@gmail.com>
 * @author Leo Mainardi <mainardi.leo@gmail.com>
 */
class m180328095726I18N_init extends Migration
{
    /**
     * @return \yii\i18n\I18N
     *
     * @throws \yii\base\InvalidConfigException
     */
    protected function getI18n()
    {
        $i18n = Yii::$app->getI18n();
        if (!$i18n instanceof I18N) {
            throw new InvalidConfigException('You should configure "i18n" component to use database before executing this migration.');
        }

        return $i18n;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \yii\base\InvalidConfigException
     */
    public function safeUp()
    {
        /** @var \yii\i18n\DbMessageSource $appSource */
        $appSource = $this->getI18n()->getMessageSource('app*');

        if (!$appSource instanceof \yii\i18n\DbMessageSource) {
            throw new InvalidConfigException('You should configure "i18n" component to use a DB source for translations.');
        }

        $tableOptions = null;

        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            // also: https://stackoverflow.com/questions/30074492/what-is-the-difference-between-utf8mb4-and-utf8-charsets-in-mysql
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        //      SOURCE MESSAGE
        $this->createTable($appSource->sourceMessageTable, [
            'id' => $this->primaryKey(),
            'category' => $this->string(),
            'message' => $this->text(),

            'KEY idx_category ([[category]])',
        ], $tableOptions);

        //      MESSAGE
        $this->createTable($appSource->messageTable, [
            'id' => $this->integer()->notNull(),
            'language' => $this->string(16)->notNull(),
            'translation' => $this->text(),

            'PRIMARY KEY ([[id]], [[language]])',
            'KEY idx_source ([[id]])',
            'CONSTRAINT fk_source FOREIGN KEY ([[id]]) REFERENCES ' . $appSource->sourceMessageTable . ' ([[id]])' .
                'ON DELETE CASCADE ON UPDATE RESTRICT',
        ], $tableOptions);

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk_message_source_message', '{{%message}}');
        $this->dropTable('{{%message}}');
        $this->dropTable('{{%source_message}}');
    }
}
