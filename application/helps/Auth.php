<?php

namespace app\helps;

class Auth extends Rds
{
    static private $config = [
        'time_line' => 86400 * 7,
    ];

    public static function Auth($user_id, $prefix = 'login_')
    {
        $token = md5(uniqid() . time() . $user_id);
        return (new Rds())->redis->setex($prefix . $token, self::$config['time_line'], $user_id) ? $token : false;
    }

    public function getAuth($token, $prefix = 'login_')
    {
        $userId = (new Rds())->redis->get($prefix . $token);
        if ($userId) {
            (new Rds())->redis->expire($prefix . $token, self::$config['time_line']);
        }
        return $userId ?: false;
    }
}