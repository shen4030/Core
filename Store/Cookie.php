<?php
namespace Core\Store;

use Core\Security\Secret;
use Core\Config;

class Cookie{

	private static $instance = null;

    private $cookieConfig;

    private function __construct()
    {
        $this->cookieConfig = Config::getConfigByKey('COOKIE_SETTING');
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

    public function getValueByKey($key = '')
    {
        $result = '';
        $key = trim($key);
        if($key && isset($_COOKIE[$key])){
            $result = $_COOKIE[$key] ? $_COOKIE[$key] : null;
        }
        return $this->decode($result);
    }

    public function setValueByKey($key, $value, $time = 3600)
    {
        setcookie($key, $this->encode($value), $time + time(), '/', DOMAIN, $this->cookieConfig['HTTPS_SECURITY']);
        return isset($_COOKIE[$key]) ? true : false;
    }

    public function delValueByKey($key, $value = '')
    {
    	setcookie($key, $this->encode($value), -1, '/', DOMAIN, $this->cookieConfig['HTTPS_SECURITY'], $this->cookieConfig['HTTP_ONLY']);
    	return isset($_COOKIE[$key]) ? false : true;
    }

    private function encode($param)
    {
        if(is_array($param)){
            foreach ($param as &$value) {
                $value = $this->encode($value);
            }
            return $param;
        }else{
            return Secret::encode($param);
        }
    }

    private function decode($param)
    {
        if(is_array($param)){
            foreach ($param as &$value) {
                $value = $this->decode($value);
            }
            return $param;
        }else{
            return Secret::decode($param);
        }
    }
}