<?php
namespace Core\Http;

use Core\Security\Filter;

/**
 * 请求处理类
 * Class Request
 * @package Core\Http
 */
class Request
{
    /* 静态实例 */
    private static $instance = null;

    private $param;

    private function __construct()
    {
        if($this->isGet()){
            $this->param = $_GET;
        }elseif($this->isPost()){
            $this->param = $_POST;
        }
        if(!empty($this->param)){
            $this->param = Filter::filterParam($this->param);
        }
    }

    /*  */
    private function __clone(){}

    /**
     * 获取静态实例
     * @return Request|null
     */
    public static function instance()
    {
        if(isset(self::$instance) && self::$instance instanceof self){
            return self::$instance;
        }
        self::$instance = new self();
        return self::$instance;
    }

    /**
     * 是否为ajax请求
     * @return bool
     */
    public function isAjax()
    {
        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){
            return true;
        }else{
            return false;
        }
    }

    /**
     * 是否为post请求
     * @return bool
     */
    public function isPost()
    {
        if(isset($_SERVER['REQUEST_METHOD']) && strtoupper($_SERVER['REQUEST_METHOD']) == 'POST'){
            return true;
        }else{
            return false;
        }
    }

    /**
     * 是否为get请求
     * @return bool
     */
    public function isGet()
    {
        if(isset($_SERVER['REQUEST_METHOD']) && strtoupper($_SERVER['REQUEST_METHOD']) == 'GET'){
            return true;
        }else{
            return false;
        }
    }

    /**
     * 获取参数(get|post)
     * @param string $key
     * @param null $default
     * @return mixed|null|string
     */
    public function param($key = '', $default = null)
    {
        if('' === $key){
            return $this->param;
        }else{
            return $this->getParamByKey($key, $default);
        }
    }

    /**
     * 获取get参数
     * @param string $key
     * @param null $default
     * @return mixed|null|string
     */
    public function get($key = '', $default = null)
    {
        $result = null;
        if($this->isGet()){
            $key = trim($key);
            if('' === $key){
                return $this->param;
            }else{
                return $this->getParamByKey($key, $default);
            }
        }
        return $result;
    }

    /**
     * 获取post参数
     * @param string $key
     * @param null $default
     * @return mixed|null|string
     */
    public function post($key = '', $default = null)
    {
        $result = null;
        if($this->isPost()){
            $key = trim($key);
            if('' === $key){
                return $this->param;
            }else{
                return $this->getParamByKey($key, $default);
            }
        }
        return $result;
    }

    protected function getParamByKey($key, $default = null)
    {
        $result = null;
        if(isset($this->param[$key])){
            $result = $this->param[$key];
        }
        if(empty($result) && !is_null($default)){
            $result = $default;
        }
        return $result;
    }

}