<?php

namespace app\helps;

use think\facade\Env;

class Rds
{
    public $redis;

    private $option = [
        'host' => 'localhost',
        'port' => 6379,
        'password' => '',
        'prefix' => 'Base_',
    ];

    public function __construct()
    {
        $this->redis = new \Redis();
        $this->redis->connect(Env::get('redis.host'), Env::get('redis.port'));
        if (Env::get('redis.password')) {
            $this->redis->auth($this->option['password']);
        }
        if (Env::get('redis.prefix')) {
            $this->redis->setOption(\Redis::OPT_PREFIX, $this->option['prefix']);
        }
        return $this->redis;
    }
}