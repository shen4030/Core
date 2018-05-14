<?php
namespace Core\Store;

class Session{

	private static $instance = null;

    private function __construct()
    {
        session_start();
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
        $result = $_SESSION;
        $key = trim($key);
        if(!empty($key)){
            $result = isset($_SESSION[$key]) && $_SESSION[$key] ? $_SESSION[$key] : null;
        }
        return $result;
    }

    public function setValueByKey($key, $value, $time = 0)
    {
        if($time !== 0){
            ini_set('session.gc_maxlifetime', $time); 
            ini_set("session.cookie_lifetime", $time);
        }
        if($value === null){
            $this->delValueByKey($key);
        }else{
            $_SESSION[$key] = $value;
        }
        return isset($_SESSION[$key]) ? true : false;
    }

    public function delValueByKey($key)
    {
        $result = false;
        if(isset($_SESSION[$key])){
            unset($_SESSION[$key]);
            $result = true;
        }
        return $result;
    }
}