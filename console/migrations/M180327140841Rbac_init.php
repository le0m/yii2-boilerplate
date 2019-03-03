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
use yii\rbac\DbManager;

/**
 * Initializes RBAC tables.
 *
 * @author Alexander Kochetov <creocoder@gmail.com>
 * @author Leo Mainardi <mainardi.leo@gmail.com>
 */
class m180327140841Rbac_init extends Migration
{
    /**
     * @return \yii\rbac\DbManager
     *
     * @throws \yii\base\InvalidConfigException
     */
    protected function getAuthManager()
    {
        $authManager = Yii::$app->getAuthManager();
        if (!$authManager instanceof DbManager) {
            throw new InvalidConfigException('You should configure "authManager" component to use database before executing this migration.');
        }

        return $authManager;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \yii\base\InvalidConfigException
     */
    public function safeUp()
    {
        // only used for table names, no need to set DB
        $authManager = $this->getAuthManager();
        $tableOptions = null;

        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            // also: https://stackoverflow.com/questions/30074492/what-is-the-difference-between-utf8mb4-and-utf8-charsets-in-mysql
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        //      AUTH RULE
        $this->createTable($authManager->ruleTable, [
            'name' => $this->string(64),
            'data' => $this->binary(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),

            'PRIMARY KEY ([[name]])',
        ], $tableOptions);

        //      AUTH ITEM
        $this->createTable($authManager->itemTable, [
            'name' => $this->string(64)->notNull(),
            'rule_name' => $this->string(64),
            'type' => $this->smallInteger()->notNull(),
            'description' => $this->text(),
            'data' => $this->binary(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),

            'PRIMARY KEY ([[name]])',
            'KEY idx_type ([[type]])',
            'KEY idx_rule_name ([[rule_name]])',
            'CONSTRAINT fk_rule_name FOREIGN KEY ([[rule_name]]) REFERENCES ' . $authManager->ruleTable . ' ([[name]])' .
                ' ON DELETE SET NULL ON UPDATE CASCADE',
        ], $tableOptions);

        //      AUTH ITEM CHILD
        $this->createTable($authManager->itemChildTable, [
            'parent' => $this->string(64)->notNull(),
            'child' => $this->string(64)->notNull(),

            'PRIMARY KEY ([[parent]], [[child]])',
            'KEY idx_parent ([[parent]])',
            'KEY idx_child ([[child]])',
            'CONSTRAINT fk_parent FOREIGN KEY ([[parent]]) REFERENCES ' . $authManager->itemTable . ' ([[name]])' .
                ' ON DELETE CASCADE ON UPDATE CASCADE',
            'CONSTRAINT fk_child FOREIGN KEY ([[child]]) REFERENCES ' . $authManager->itemTable . ' ([[name]])' .
                ' ON DELETE CASCADE ON UPDATE CASCADE',
        ], $tableOptions);

        //      AUTH ASSIGNMENT
        $this->createTable($authManager->assignmentTable, [
            'item_name' => $this->string(64)->notNull(),
            'user_id' => $this->integer(11)->notNull(),
            'created_at' => $this->integer(),

            'PRIMARY KEY ([[item_name]], [[user_id]])',
            'KEY idx_user ([[user_id]])',
            'KEY idx_item_name ([[item_name]])',
            'CONSTRAINT fk_item_name FOREIGN KEY ([[item_name]]) REFERENCES ' . $authManager->itemTable . ' ([[name]])' .
                ' ON DELETE CASCADE ON UPDATE CASCADE',
            'CONSTRAINT fk_user FOREIGN KEY ([[user_id]]) REFERENCES {{%user}} ([[id]])' .
                ' ON DELETE CASCADE ON UPDATE CASCADE',
        ], $tableOptions);

        return true;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \yii\base\InvalidConfigException
     */
    public function safeDown()
    {
        $authManager = $this->getAuthManager();
        $this->db = $authManager->db;

        $this->dropTable($authManager->assignmentTable);
        $this->dropTable($authManager->itemChildTable);
        $this->dropTable($authManager->itemTable);
        $this->dropTable($authManager->ruleTable);
    }
}
