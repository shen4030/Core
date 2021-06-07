<?php
namespace Core\Model;

use Core\Client\MySqlClient;
use Core\Security\Filter;

class Model{

    /* 对象池 */
    private static $instance = null;

    protected $client = null;

    protected $tableName = '';

    protected $sql = '';
    protected $value = [];

    private $alias = '';
    private $field = '*';
    private $where = [];
    private $order = '';
    private $group = '';
    private $limit = '';

    public function __construct($tableName = '')
    {
        $this->client = MySqlClient::instance()->getPDOClient();
        if(empty($this->tableName)){
            $this->tableName = trim($tableName);
        }
    }

    /**
     * 从实例池获取实例
     * @param string $model
     * @return Model
     */
    public static function instance($model = '')
    {
        if(!is_string($model)){
            return false;
        }
        $model = trim(Filter::filterString($model));
        if(isset(self::$instance[$model]) && self::$instance[$model] instanceof self){
            return self::$instance[$model];
        }
        self::$instance[$model] = new self($model);
        return self::$instance[$model];
    }

    /**
     * 插入数据
     * @param array $data
     * @return bool|string
     */
    public function insert(array $data)
    {
        if(is_array($data) && $data){
            $col = [];
            $seat = [];
            foreach ($data as $key => $value){
                $key = trim($key);
                $col[] = sprintf('`%s`', $key);
                $seat[] = ":$key";
                $this->value[":$key"] = $value;
            }
            /*列*/
            $col = sprintf('(%s)', implode(',', $col));
            /*占位*/
            $seat = sprintf('(%s)', implode(',', $seat));

            $this->sql = "INSERT INTO $this->tableName $col VALUES $seat";
            $stmt = $this->client->prepare($this->sql);
            $stmt->execute($this->value);
            return $this->client->lastInsertId();
        }
        return false;
    }

    /**
     * where语句
     * @param string $where
     * @return $this
     */
    public function where($where = '')
    {
        if(is_string($where)){
            $this->where = trim($where);
        }elseif(is_array($where)){
            foreach ($where as $key => $value){
                $key = trim($key);
                $operation = reset($value);
                $value = end($value);
                switch (strtolower($operation)){
                    case 'eq':
                        $this->whereEq($key, $value);
                        break;
                    case 'lt':
                        $this->whereLt($key, $value);
                        break;
                    case 'like':
                        $this->whereLike($key, $value);
                        break;
                    case 'gt':
                        $this->whereGt($key, $value);
                        break;
                    case 'neq':
                        $this->whereNeq($key, $value);
                        break;
                    case 'egt':
                        $this->whereEgt($key, $value);
                        break;
                    case 'elt':
                        $this->whereElt($key, $value);
                        break;
                    case 'in':
                        $this->whereIn($key, $value);
                        break;
                }
            }
        }
        return $this;
    }

    /**
     * where条件 “等于”
     * @param $field
     * @param $value
     * @return $this
     */
    public function whereEq($field, $value)
    {
        $field = trim($field);
        $this->where[] = sprintf('%s = :%s', $field, $field);
        $this->value[":$field"] = $value;
        return $this;
    }

    /**
     * where条件 “大于”
     * @param $field
     * @param $value
     * @return $this
     */
    public function whereGt($field, $value)
    {
        $field = trim($field);
        $this->where[] = sprintf('%s > :%s', $field, $field);
        $this->value[":$field"] = $value;
        return $this;
    }

    /**
     * where条件 “小于”
     * @param $field
     * @param $value
     * @return $this
     */
    public function whereLt($field, $value)
    {
        $field = trim($field);
        $this->where[] = sprintf('%s < :%s', $field, $field);
        $this->value[":$field"] = $value;
        return $this;
    }

    /**
     * where条件 “大于等于”
     * @param $field
     * @param $value
     * @return $this
     */
    public function whereEgt($field, $value)
    {
        $field = trim($field);
        $this->where[] = sprintf('%s >= :%s', $field, $field);
        $this->value[":$field"] = $value;
        return $this;
    }

    /**
     * where条件 “小鱼等于”
     * @param $field
     * @param $value
     * @return $this
     */
    public function whereElt($field, $value)
    {
        $field = trim($field);
        $this->where[] = sprintf('%s <= :%s', $field, $field);
        $this->value[":$field"] = $value;
        return $this;
    }

    /**
     * where条件 “不等于”
     * @param $field
     * @param $value
     * @return $this
     */
    public function whereNeq($field, $value)
    {
        $field = trim($field);
        $this->where[] = sprintf('%s <> :%s', $field, $field);
        $this->value[":$field"] = $value;
        return $this;
    }

    /**
     * where条件 “IN”
     * @param $field
     * @param array $value
     * @return $this
     */
    public function whereIn($field, array $value)
    {
        $field = trim($field);

        $this->where[] = sprintf('%s IN (%s)',
            $field,
            implode(',', array_values($value))
        );

        return $this;
    }

    /**
     * where条件 “LIKE”
     * @param $field
     * @param $value
     * @return $this
     */
    public function whereLike($field, $value)
    {
        $field = trim($field);
        $this->where[] = sprintf("%s LIKE :%s", $field, $field);
        $this->value[":$field"] = $value;
        return $this;
    }

    /**
     * 返回数据集合
     * @return array
     */
    public function select()
    {
        $stmt = $this->getDataStmt();
        $this->clearParse();
        return $stmt->fetchAll();
    }

    /**
     * 返回单一模型
     * @return mixed
     */
    public function find()
    {
        $stmt = $this->getDataStmt();
        $this->clearParse();
        return $stmt->fetch();
    }

    /**
     * 返回某个字段的值
     * @param $field
     * @return mixed|string
     */
    public function getField($field)
    {
        $result = '';
        if(is_string($field)){
            $field = trim($field);
            $where = $this->getWhereParse();
            $sql = "SELECT $field FROM $this->tableName";
            if($where){
                $sql .= " WHERE $where";
            }
            $stmt = $this->getDataStmt($sql);
            $result = $stmt->fetchColumn(0);
            $this->clearParse();
        }
        return $result;
    }

    /**
     * @param $alias
     * @return $this
     */
    public function alias($alias)
    {
        $this->alias = trim($alias);
        return $this;
    }

    /**
     * group语句
     * @param $group
     * @return $this
     */
    public function group($group)
    {
        $this->group = trim($group);
        return $this;
    }

    /**
     * order语句
     * @param $field
     * @param string $order
     * @return $this
     */
    public function order($field, $order = '')
    {
        $field = trim($field);
        $order = strtolower($order);
        switch ($order){
            case 'desc' :
                $this->order = sprintf('%s DESC', $field);
                break;
            case 'asc' :
                $this->order = sprintf('%s ASC', $field);
                break;
            case '' :
                $this->order = $field;
                break;
        }
        return $this;
    }

    /**
     * 查询字段集合
     * @param $field
     * @return $this
     */
    public function field($field)
    {
        if($field) {
            if (is_array($field)) {
                foreach ($field as &$value){
                    $value = sprintf('`%s`', $value);
                }
                $field = implode(',', array_values($field));
            }
            $this->field = $field;
        }
        return $this;
    }

    /**
     * limit语句
     * @param $startIndex
     * @param null $pageSize
     * @return $this
     */
    public function limit($startIndex, $pageSize = null)
    {
        if(is_null($pageSize)){
            $start = 0;
            $limit = intval($startIndex);
        }else{
            $start = intval($startIndex);
            $limit = intval($pageSize);
        }
        $this->limit = sprintf('%d , %d', $start, $limit);
        return $this;
    }

    /**
     * @param $pageNumber
     * @param int $pageSize
     * @return Model
     */
    public function page($pageNumber, $pageSize = 10)
    {
        $startIndex = ceil((intval($pageNumber) - 1) * $pageSize);
        return $this->limit($startIndex, $pageSize); 
    }

    /**
     * 更新数据操作
     * @param array $data
     * @return bool|int
     */
    public function save(array $data)
    {
        if(!$this->where){
            return false;
        }
        if($data){
            $this->sql = "UPDATE $this->tableName SET ";
            $set = [];
            foreach ($data as $key => $value){
                $set[] = sprintf("`%s` = :%s_update", $key, $key);
                $this->value[":$key".'_update'] = $value;
            }
            $this->sql .= implode(',', $set);
            $this->sql .= ' WHERE ' . $this->getWhereParse();
            $stmt = $this->client->prepare($this->sql);
            $stmt->execute($this->value);
            $this->clearParse();
            return $stmt->rowCount();
        }
        return false;
    }

    public function query($query)
    {
        return $this->client->query($query)->fetchAll();
    }

    public function getLastSql()
    {
        return $this->sql;
    }

    private function getWhereParse()
    {
        foreach ($this->where as &$query){
            $query = sprintf('(%s)', $query);
        }
        $where = implode(' AND ', $this->where);
        return $where;
    }

    private function getSelectParse()
    {
        $this->sql = "SELECT ";
        if($this->field){
            $this->sql .= $this->field . ' FROM ' .$this->tableName;
        }
        if($this->alias){
            $this->sql .= ' AS ' . $this->alias;
        }
        if($this->where){
            $where = $this->where;
            if(is_array($this->where)){
                $where = $this->getWhereParse();
            }
            $this->sql .= ' WHERE ' . $where;
        }
        if($this->group){
            $this->sql .= ' GROUP BY ' . $this->group;
        }
        if($this->order){
            $this->sql .= ' ORDER BY '. $this->order;
        }
        if($this->limit){
            $this->sql .= ' LIMIT '. $this->limit;
        }
        return $this->sql;
    }

    private function getDataStmt($sql = '')
    {
        if(empty($sql)){
            $sql = $this->getSelectParse();
        }

        if($this->value){
            # 有参数绑定
            $stmt = $this->client->prepare($sql);
            $stmt->execute($this->value);
        }else{
            # 无参数绑定
            $stmt = $this->client->query($sql);
        }

        return $stmt;
    }

    private function clearParse()
    {
        $this->alias = '';
        $this->field = '*';
        $this->where = [];
        $this->order = '';
        $this->group = '';
        $this->limit = '';
        $this->sql = '';
        $this->value = [];
    }
}