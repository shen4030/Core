<?php

$pathInfo = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '';

$routeConfig = \Core\Config::getConfigByKey('ROUTE_SETTING');
if(isset($routeConfig[$pathInfo]) && $routeConfig[$pathInfo]){
    $pathInfo = $routeConfig[$pathInfo];
}

$module = '';
$className = '';
$action = '';
if($pathInfo){
	$pathInfo = array_values(array_filter(explode('/', $pathInfo)));
	$module = trim($pathInfo[0]);
	$className = trim($pathInfo[1]);
	$action = trim($pathInfo[2]);
}

# 默认模块名称
if(empty($className)){
	$module = DEFAULT_MODULE;
}
# 默认控制器名称
if(empty($className)){
	$className = DEFAULT_CONTROLLER;
}
# 默认方法名称
if(empty($action)){
	$action = DEFAULT_ACTION;
}

# 常量定义
define('MODULE_NAME', $module);
define('CONTROLLER_NAME', $className);
define('ACTION_NAME', $action);

$className = '\Controller\\' . $module . '\\' . $className;

if(class_exists($className)){
	$controller = new $className();
	if(method_exists($controller, $action)){
		$controller->$action();
	}else{
		exit('404');
	}
}else{
	echo "暂未开放";
}