<?php
namespace Core\Client;

use Core\Config;

class MySqlClient{

	private static $instance = null;

	private $pdoClient = null;

	private function __construct()
	{
        $config = Config::getConfigByKey('MYSQL_SETTING');
        $dsn = sprintf('mysql:dbname=%s;host=%s', $config['DB_NAME'], $config['DB_HOST']);
        $option = array(
            \PDO::ATTR_PERSISTENT => true,
            \PDO::ATTR_ERRMODE => 2,
            \PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
        );
		$this->pdoClient = new \PDO($dsn, $config['DB_USER'], $config['DB_PASSWORD'], $option);
        $this->pdoClient->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);
        $this->pdoClient->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
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

    public function getPDOClient()
    {
    	return $this->pdoClient;
    }
}