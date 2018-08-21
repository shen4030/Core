<?php
namespace Core\Store;

use Core\Config;

class Queue{

    private $connection;

    public function __construct()
    {
        $queueConfig = Config::getConfigByKey('QUEUE_SETTING');
        if(class_exists('AMQPConnection')){
            $this->connection = new \AMQPConnection($queueConfig);

            $connect = $this->connection->connect();
            if($connect){

            }else{

            }
        }

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