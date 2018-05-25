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

    /**
     * 由键获取值
     * @param $key
     * @return bool|string
     */
    public function getValueByKey($key)
    {
        if($this->redisClient){
            return $this->redisClient->get($key);
        }
        return '';
    }

    /**
     * 为键设定值
     * @param $key
     * @param $value
     * @param int $time
     * @return bool
     */
    public function setValueByKey($key, $value, $time = 3600)
    {
        if(empty($this->redisClient)){
            return '';
        }
        if(is_array($value)){
            $result = $this->redisClient->hset($key, $value, $time);
        }else{
            $result = $this->redisClient->set($key, $value, $time);
        }
        return strtolower($result) == 'ok' ? true : false;
    }
}