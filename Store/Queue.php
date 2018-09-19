<?php
namespace Core\Store;

use Core\Config;

/**
 * 消息队列
 */
class Queue{

    /* 连接 */
    private static $connection;
    private static $connect;

    /* 频道 */
    private static $channel = [];

    /* 交换机 */
    private static $exchange = [];

    /* 队列 */
    private static $queue = [];

    private static $instance = null;

    private function __construct()
    {
        $queueConfig = Config::getConfigByKey('QUEUE_SETTING');
        if(class_exists('AMQPConnection')){
            self::$connection = new \AMQPConnection($queueConfig);
            self::$connect = self::$connection->connect();
            date_default_timezone_set("Asia/Shanghai");
        }else{
            echo '没有安装AMQP拓展';
        }
    }

    /**
     * 创建单例
     * @return [object] Queue
     */
    public static function instance()
    {
        if(isset(self::$instance) && self::$instance instanceof self){
            return self::$instance;
        }
        self::$instance = new self();
        return self::$instance;
    }

    private function __clone(){}

    public function __set($name, $value)
    {
        $this->$name = $value;
        return $this;
    }

    public function __get($name)
    {
        return isset($this->$name) ? $this->$name : null;
    }

    /**
     * 获取交换机实例
     * @param  [string] $exName  交换机名称
     * @param  [string] $channel 频道名称
     * @return [object] 交换机实例
     */
    public function getExchange($exName, $channel)
    {
        $index = $exName . '_' . $channel;
        if(isset(self::$exchange[$index]) && self::$exchange[$index]){
            return self::$exchange[$index];
        }
        $channel = $this->getChannel($channel);
        $exchange = new \AMQPExchange($channel);
        $exchange->setName($exName); 
        $exchange->setType(AMQP_EX_TYPE_DIRECT);  
        $exchange->setFlags(AMQP_DURABLE);
        $exchange->declare();

        self::$exchange[$index] = $exchange;
        return $exchange;
    }

    /**
     * 获取频道实例
     * @param  [string] $channelName 频道名称
     * @return [object] 频道实例
     */
    public function getChannel($channelName)
    {
        if(isset(self::$channel[$channelName]) && self::$channel[$channelName]){
            return self::$channel[$channelName];
        }

        /* 创建新频道 */
        $channel = new \AMQPChannel(self::$connection);
        self::$channel[$channelName] = $channel;
        return $channel;
    }

    /**
     * 获取队列实例
     * @param  [string] $queueName 队列名称
     * @param  [string] $channel 频道名称
     * @return [object] 队列实例
     */
    public function getQueue($queueName, $channel)
    {
        $index = $queueName . '_' . $channel;
        if(isset(self::$queue[$index]) && self::$queue[$index]){
            return self::$queue[$index];
        }
        $channel = $this->getChannel($channel);
        /* 创建新队列 */
        $queue = new \AMQPQueue($channel);
        $queue->setName($queueName);
        /* 持久 */
        $queue->setFlags(AMQP_DURABLE);
        $queue->declare();

        self::$queue[$index] = $queue;
        return $queue;
    }

    /**
     * 发布消息
     * @param  [string] $message  消息
     * @param  [string] $exchange 交换机名称
     * @param  [string] $channel  频道名称
     * @param  [string] $routeKey 路由键
     * @return [boolean] $result 发布是否成功
     */
    public function publish($message, $exchange, $channel, $routeKey)
    {
        $exchange = $this->getExchange($exchange, $channel);
        $result = $exchange->publish($message, $routeKey);
        return $result;
    }

    /**
     * 接受消息
     * @param  [string] $queue    队列名称
     * @param  [string] $exName   交换机名称
     * @param  [string] $channel  频道名称
     * @param  [string] $routeKey 路由键
     * @param  [function] $callback ($envelope, $queue)
     */
    public function consumer($queue, $exName, $channel, $routeKey, $callback)
    {
        $queue = $this->getQueue($queue, $channel);
        $exchange = $this->getExchange($exName, $channel);
        $queue->bind($exName, $routeKey);

        while (true) {
            $queue->consume($callback);
        }
    }
}