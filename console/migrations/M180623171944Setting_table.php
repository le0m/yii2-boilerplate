<?php

namespace console\migrations;

use yii\db\Migration;

/**
 * Initializes settings table.
 *
 * @author Leo Mainardi <mainardi.leo@gmail.com>
 */
class M180623171944Setting_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;

        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            // also: https://stackoverflow.com/questions/30074492/what-is-the-difference-between-utf8mb4-and-utf8-charsets-in-mysql
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        //      SETTING
        $this->createTable('{{%setting}}', [
            'id' => $this->primaryKey(11),
            'name' => $this->string(255)->notNull(),
            'value' => $this->string(255)->notNull(),
            'description' => $this->text(),
            'created_at' => $this->integer(11)->notNull(),
            'updated_at' => $this->integer(11)->notNull(),
        ], $tableOptions);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%setting}}');

        return true;
    }
}
