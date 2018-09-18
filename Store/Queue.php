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

    public function __construct()
    {
        $queueConfig = Config::getConfigByKey('QUEUE_SETTING');
        if(class_exists('AMQPConnection')){
            self::$connection = new \AMQPConnection($queueConfig);
            self::$connect = self::$connection->connect();
        }else{
            echo '没有安装AMQP拓展';
        }
    }

    public function createQueue($queueName)
    {
        if(self::$queue[$qu]){

        }
    }

    public function getExchange($exName, $channel)
    {
        if(isset(self::$exchange[$exName]) && self::$exchange[$exName]){
            return self::$exchange[$exName];
        }

        $exchange = new \AMQPExchange($channel);
        $exchange->setName($exName); 
        $exchange->setType(AMQP_EX_TYPE_DIRECT);  
        $exchange->setFlags(AMQP_DURABLE);

        self::$exchange[$exName] = $exchange;
        return $exchange;
    }

    public function getExchangeStatus($exchange)
    {
        return $exchange->declare();
    }

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

    public function getQueue($queueName)
    {
        if(isset(self::$queue[$queueName]) && self::$queue[$queueName]){
            return self::$queue[$queueName];
        }

        /* 创建新队列 */
        $queue = new \AMQPQueue($channel);
        $queue->setName($queueName);
        /* 持久 */
        $queue->setFlags(AMQP_DURABLE);

        self::$queue[$queueName] = $queue;
        return $queue;
    }

    public function consumer($queue, $exName, $routeKey)
    {
        $queue->bind($exName, $routeKey);
    }

    public function getCan(){
        
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


}