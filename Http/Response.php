<?php
namespace Core\Http;

class Response{

	public function __construct(){}

	public static function htmlReturn($htmlSrc, $param = [])
	{
		if(!empty($param)){
			$keys = array_keys($param);
			foreach ($keys as $value) {
				$$value = $param[$value];
			}
		}
		unset($param);
		include_once VIEW_DOCUMENT . $htmlSrc . '.php';
		exit;
	}

	public static function jsonReturn($data)
	{
		header('Content-Type:application/json');
		exit(json_encode($data));
	}

	public static function redirect($src)
	{
	    $src = url($src);
		header('Location:' . $src);
	}
}