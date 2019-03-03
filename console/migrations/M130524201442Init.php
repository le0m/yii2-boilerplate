<?php

namespace console\migrations;

use yii\db\Migration;


/**
 * Initializes user table.
 *
 * @author Leo Mainardi <mainardi.leo@gmail.com>
 */
class M130524201442Init extends Migration
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

        $this->createTable('{{%user}}', [
            'id' => $this->primaryKey(11),
            'username' => $this->string(190)->notNull()->unique(),
            'auth_key' => $this->string(32)->notNull(),
            'password_hash' => $this->string(190)->notNull(),
            'password_reset_token' => $this->string(190)->unique(),
            'account_activation_token' => $this->string(190),
            'failed_logins' => $this->integer(11)->defaultValue(0)->notNull(),
            'last_failed_login' => $this->integer(11),
            'email' => $this->string(190)->notNull()->unique(),
            'language' => $this->string(45),
            'timezone' => $this->string(190),
            'status' => $this->smallInteger(4)->defaultValue(5)->notNull(),
            'created_at' => $this->integer(11)->notNull(),
            'updated_at' => $this->integer(11)->notNull(),
        ], $tableOptions);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%user}}');

        return false;
    }
}
