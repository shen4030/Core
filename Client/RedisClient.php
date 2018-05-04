<?php
namespace Core\Client;

use Core\Config;

class RedisClient{

	private static $instance;

	private $host = '';

	private $port = '';

	private $connection = null;

	public static function create()
	{
		if(isset(self::$instance) && self::$instance instanceof self){
			return self::$instance;
		}
		self::$instance = new self();
		return self::$instance;
	}

	private function __construct()
	{
		$config = Config::getConfigByKey('REDIS_SETTING');
		$this->host = trim($config['DB_HOST']);
		$this->port = $config['DB_PORT'];
		$this->connection = new \Redis();
		$this->connection->connect($this->host, $this->port);
	}

	public function getConn()
	{
		return $this->connection;
	}

	
}