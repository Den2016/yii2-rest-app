<?php

use yii\db\Migration;

class m191128_102500_add_jwt_tokens_tables_for_users extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%usertoken}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'valid_atoken' => $this->tinyInteger()->notNull(),
            'valid_rtoken' => $this->tinyInteger()->notNull(),
            'issued_time' => $this->dateTime()->notNull(),
            'expires' => $this->dateTime()->notNull(),
            'last_login' => $this->dateTime(),
            'last_ip' => $this->string(20),
            'auth_token' => $this->string(512)->notNull(),
            'refresh_token' => $this->string(512)->notNull(),
            'user_agent' => $this->string(200)->notNull(),
        ], $tableOptions);
        // creates index for column `author_id`
        $this->createIndex(
            'idx-usertoken-user_id',
            '{{%usertoken}}',
            'user_id'
        );
    }

    public function down()
    {
        // drops index for column `category_id`
        $this->dropIndex(
            'idx-usertoken-user_id',
            '{{%usertoken}}'
        );
        $this->dropTable('{{%usertoken}}');
    }

}