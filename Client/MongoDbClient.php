<?php
namespace Core\Client;

use Core\Config;

class MongoDbClient{

	/*静态实例池*/
	private static $instance = null;
	/*主机地址*/
	private $host = '127.0.0.1';
	/*端口号*/
	private $port = '27017';
	

	private $connection = null;
	private $datebase = '';
	private $collection = '';
	private $config = '';

	private function __construct($collection, $datebase, $uriOptions, $driverOptions)
	{
		$mongodbConfig = Config::getConfigByKey('MONGODB_SETTING');

		$this->host = trim($mongodbConfig['DB_HOST']);
		$this->port = trim($mongodbConfig['DB_PORT']);
		$this->collection = trim($collection);
		$this->datebase = trim($datebase);
		$this->config = sprintf('%s.%s', $datebase, $collection);

		$this->connection = new \MongoDB\Driver\Manager(
			sprintf('mongodb://%s:%s/', $this->host, $this->port),
            $uriOptions,
            $driverOptions
		);
	}

	private function __clone(){}

	public static function getMongoDbClient($collection, $datebase = 'test', array $uriOptions = [], array $driverOptions = [])
	{
		$config = $collection. '.' . $datebase;
		if(isset(self::$instance[$config]) && self::$instance[$config] instanceof self){
			return self::$instance[$config];
		}
		self::$instance[$config] = new self($collection, $datebase, $uriOptions, $driverOptions);
		return self::$instance[$config];
	}

	/* 插入文档 */
	public function insert($document, $id = null)
	{	
		if(is_null($id)){
			$_id = ['_id' => new \MongoDB\BSON\ObjectID];
		}else{
			$_id = ['_id' => $id];
		}

		$data = array_merge($_id, $document);
		$bulk = new \MongoDB\Driver\BulkWrite;
		$bulk->insertOne($document);
		$writeConcern = new \MongoDB\Driver\WriteConcern(MongoDB\Driver\WriteConcern::MAJORITY, 1000);
		$result = $this->connection->executeBulkWrite($this->config, $bulk, $writeConcern);
	}

	/* 更新文档 */
	public function update($filter, $query, $option = ['multi' => false, 'upsert' => false])
	{ 
		$bulk = new \MongoDB\Driver\BulkWrite;
		$bulk->update($filter, $query, $option);  
		$writeConcern = new \MongoDB\Driver\WriteConcern(MongoDB\Driver\WriteConcern::MAJORITY, 1000);
		$this->connection->executeBulkWrite($this->config, $bulk, $writeConcern);
	}

	/* 删除文档 */
	public function delete($filter, $option = ['limit' => 1])
	{
		$this->bulk->delete($filter);
		$writeConcern = new \MongoDB\Driver\WriteConcern(MongoDB\Driver\WriteConcern::MAJORITY, 1000);
		$this->connection->executeBulkWrite($this->config, $this->bulk, $writeConcern);
	}

	/* 查询文档 */
	public function select($filter, $option =[])
	{
		$query = new \MongoDB\Driver\Query($filter, $option);
		$cursor = $this->connection->executeQuery($this->config, $query);
		$cursor->setTypeMap(['root' => 'array', 'document' => 'array', 'array' => 'array']);
		$data = $cursor->toArray();
		return $data;
	}
}