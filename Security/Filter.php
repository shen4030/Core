<?php
namespace Core\Security;

class Filter{

    /**
     * 过滤字符串
     * @param $str
     * @return mixed|string
     */
	public static function filterString($str)
	{
	    if(!is_string($str)){
	        return false;
        }
   		$farr = array(
         	"/<(\\/?)(script|i?frame|style|html|body|title|link|meta|object|\\?|\\%)([^>]*?)>/isU",
         	"/(<[^>]*)on[a-zA-Z]+\s*=([^>]*>)/isU",
           	"/select\b|insert\b|update\b|delete\b|drop\b|;|\"|\'|\/\*|\*|\.\.\/|\.\/|union|into|load_file|outfile|dump/is"
       	);
        $str = preg_replace($farr, '', $str);
        $str = htmlspecialchars($str);
        return $str;
	}

    /**
     * 过滤数据
     * @param $str
     * @return mixed|string
     */
	public static function filterParam($param)
	{
		if(is_array($param)){
			foreach ($param as &$value) {
				$value = self::filterParam($value);
			}
			return $param;
		}else{
			return self::filterString($param);
		}
	}

}