<?php
namespace Core\Http;

class Router{

	public static function url($url = '', $param = [])
	{
		if(empty($url)){
			$url = CONTROLLER_NAME . '/' . ACTION_NAME;
		}
	    $src = BASE_URL . MODULE_NAME . '/' .$url;
	    if($param){
	        $src .= '?' . http_build_query($param);
        }
        return $src;
	}
}