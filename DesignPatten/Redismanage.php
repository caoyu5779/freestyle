<?php
/**
 * Created by PhpStorm.
 * User: pengcheng8
 * Date: 17/9/19
 * Time: 下午3:43
 * DESC:
 */

class RedisManager
{
    private static $_redisInstance;

    private static $_connectSource;

    const REDIS_KEY = 'msg';

    private $_redisConfig = [
        'host' => '127.0.0.1',
        'port' => '6379'
    ];

    private function __construct()
    {
        self::$_connectSource = new Redis();
        self::$_connectSource->connect($this->_redisConfig['host'],$this->_redisConfig['port']);
    }

    private function __clone()
    {
        // TODO: Implement __clone() method.
    }

    public static function getInstance()
    {
        if (!self::$_redisInstance)
        {
            self::$_redisInstance = new self();

        }

        return self::$_redisInstance;
    }

    public function addMsg($msg)
    {
        $res = self::$_connectSource->lPush(self::REDIS_KEY, $msg);

        return $res;
    }

    public function consumeMsg()
    {
        $res = self::$_connectSource->rPop(self::REDIS_KEY);

        return $res;
    }

}
