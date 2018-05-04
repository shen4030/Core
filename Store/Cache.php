<?php
namespace Core\Store;

use Core\Client\RedisClient;

class Cache{

    private static $instance = null;

    private $redisClient;

    private function __construct()
    {
        $this->redisClient = RedisClient::create()->getConn();
    }

    private function __clone(){}

    public static function instance()
    {
        if(isset(self::$instance) && self::$instance instanceof self){
            return self::$instance;
        }
        self::$instance = new self();
        return self::$instance;
    }

    public function getValueByKey($key)
    {
        $this->redisClient->get($key);
    }

    public function setValueByKey($key, $value, $time = 3600)
    {
        $this->redisClient->set($key, $value, $time);
    }
}