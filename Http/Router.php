<?php
namespace Core\Http;

class Router{

	public static function url($url, $param = [])
	{
	    $src = BASE_URL . MODULE_NAME . '/' .$url;
	    if($param){
	        $src .= '?' . http_build_query($param);
        }
        return $src;
	}
}