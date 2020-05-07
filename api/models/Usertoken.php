<?php


namespace api\models;

use Yii;
use yii\behaviors\AttributeBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "usertoken".
 *
 * @property int $id
 * @property int $user_id
 * @property int $valid_atoken
 * @property int $valid_rtoken
 * @property string $expires
 * @property string $last_login
 * @property string $last_ip
 * @property string $auth_token
 * @property string $refresh_token
 * @property string $issued_time
 * @property string $user_agent
 */
class Usertoken extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%usertoken}}';
    }


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'auth_token', 'refresh_token', 'user_agent'], 'required'],
            [['user_id', 'valid_atoken', 'valid_rtoken'], 'integer'],
            [['issued_time', 'expires', 'last_login'], 'safe'],
            [['auth_token', 'refresh_token'], 'string', 'max' => 512],
            [['last_ip'], 'string', 'max' => 20],
            [['user_agent'], 'string', 'max' => 200],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'auth_token' => 'Auth Token',
            'valid_atoken' => 'Valid Auth Token',
            'valid_rtoken' => 'Valid Refresh Token',
            'refresh_token' => 'Refresh Token',
            'expires' => 'Expires',
            'last_login' => 'Last login',
            'last_ip' => 'Last IP',
            'issued_time' => 'Issued Time',
            'user_agent' => 'User Agent',
        ];
    }
}
