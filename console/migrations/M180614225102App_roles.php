<?php

namespace console\migrations;

use common\models\AuthItem;
use Yii;
use yii\base\InvalidConfigException;
use yii\db\Migration;
use yii\rbac\DbManager;

/**
 * Initializes basic RBAC roles.
 *
 * @author Leo Mainardi <mainardi.leo@gmail.com>
 */
class M180614225102App_roles extends Migration
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
        $tableOptions = null;
        $authManager = $this->getAuthManager();

        // if a DB is given to the migration script, it's because we want to apply to it
        if ($this->db != $authManager->db) {
            $authManager->db = $this->db;
        }

        try {
            //      'user' role
            $user = $authManager->createRole(AuthItem::ROLE_USER);
            $user->description = "Registered user";
            $authManager->add($user);

            //      'admin' role
            $admin = $authManager->createRole(AuthItem::ROLE_ADMIN);
            $admin->description = "Administrator";
            $authManager->add($admin);

            $authManager->addChild($admin, $user);
        }
        catch (\Exception $e) {
            echo 'Error: ' . $e->getMessage() . PHP_EOL;
            echo $e->getTraceAsString();

            return false;
        }

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

        // if a DB is given to the migration script, it's because we want to apply to it
        if ($this->db != $authManager->db) {
            $authManager->db = $this->db;
        }

        try {
            $authManager->removeAll();
        }
        catch (\Exception $e) {
            echo 'Error: ' . $e->getMessage() . PHP_EOL;
            echo $e->getTraceAsString();

            return false;
        }

        return true;
    }
}
