<?php

use yii\db\Migration;

/**
 * Class m190609_165044_users
 */
class m190609_165044_users extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        // If exist module `Users` set foreign key `user_id` to `users.id`
        if (class_exists('\wdmg\users\models\Users')) {
            $this->insert('{{%users}}', [
                'id' => 100,
                'username' => 'admin',
                'auth_key' => Yii::$app->security->generateRandomString(),
                'password_hash' => Yii::$app->security->generatePasswordHash('admin'),
                'password_reset_token' => Yii::$app->security->generateRandomString() . '_' . time(),
                'email' => 'admin@example.com',
                'email_confirm_token' => '',
                'status' => \wdmg\users\models\Users::USR_STATUS_ACTIVE,
            ]);
        }

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        if (class_exists('\wdmg\users\models\Users'))
            $this->delete('{{%users}}', '`id` = 100');
    }
}
