<?php
/**
 * 生成css
 */
if(!function_exists('show_css')){
    function show_css($src = '')
    {
        $version = \Core\Config::getConfigByKey('STATIC_VERSION');
        return '<link href="'.$src.'?v='.$version.'" rel="stylesheet">';
    }
}


/**
 * 生成js
 */
if(!function_exists('show_js')) {
    function show_js($src = '')
    {
        $version = \Core\Config::getConfigByKey('STATIC_VERSION');
        return '<script type="text/javascript" src="' . $src . '?v=' . $version . '"></script>';
    }
}

/**
 * 包含html
 */
if(!function_exists('include_html')) {
    function include_html($htmlSrc, $param = [])
    {
        if (!empty($htmlSrc)) {
            if (!empty($param)) {
                $keys = array_keys($param);
                foreach ($keys as $value) {
                    $$value = $param[$value];
                }
                unset($param);
            }
            include_once VIEW_DOCUMENT . $htmlSrc . '.php';
        }
    }
}

/**
 * session 助手函数
 */
if(!function_exists('session')) {
    function session($key, $value = '', $pretime = 0)
    {
        $key = trim($key);
        $session = \Core\Store\Session::instance();
        if ($value === '' && $pretime === 0) {
            return $session->getValueByKey($key);
        }
        if ($key && $value) {
            return $session->setValueByKey($key, $value, $pretime);
        }
        if (is_null($value)) {
            return $session->delValueByKey($key);
        }
    }
}

/**
 * cookie 助手函数
 */
if(!function_exists('cookie')) {
    function cookie($key, $value = '', $pretime = 3600)
    {
        $key = trim($key);
        $cookie = \Core\Store\Cookie::instance();
        if ($value === '' && $pretime === 3600) {
            return $cookie->getValueByKey($key);
        }
        if ($key && $value) {
            return $cookie->setValueByKey($key, $value, $pretime);
        }
        if (is_null($value)) {
            return $cookie->delValueByKey($key);
        }
    }
}

if(!function_exists('table')) {
    function table($model)
    {
        return \Core\Model\Model::instance($model);
    }
}

if(!function_exists('model')) {
    function model($model)
    {
        return \Model\Model::instance($model);
    }
}

if(!function_exists('url')) {
    function url($url, $param = [])
    {
        return \Core\Http\Router::url($url, $param);
    }
}

if(!function_exists('show_status')) {
    function show_status($status)
    {
        $result = '';
        switch ($status){
            case 1 :
                $result = '<font color="green">启用</font>';
                break;
            case 0 :
                $result = '<font color="red">禁用</font>';
        }

        return $result;
    }
}

if(!function_exists('dump')){
    function dump($var, $echo=true, $label=null, $strict=true) {
        $label = ($label === null) ? '' : rtrim($label) . ' ';
        if (!$strict) {
            if (ini_get('html_errors')) {
                $output = print_r($var, true);
                $output = '<pre>' . $label . htmlspecialchars($output, ENT_QUOTES) . '</pre>';
            } else {
                $output = $label . print_r($var, true);
            }
        } else {
            ob_start();
            var_dump($var);
            $output = ob_get_clean();
            if (!extension_loaded('xdebug')) {
                $output = preg_replace('/\]\=\>\n(\s+)/m', '] => ', $output);
                $output = '<pre>' . $label . htmlspecialchars($output, ENT_QUOTES) . '</pre>';
            }
        }
        if ($echo) {
            echo($output);
            return null;
        }else
            return $output;
    }
}

if(!function_exists('get_article_img'))
{
    function get_article_img($content, $isEncode = true)
    {
        if($isEncode){
            $content = htmlspecialchars_decode($content);
        }
    
        $pattern="/<[img|IMG].*?src=[\'|\"](.*?(?:[\.gif|\.jpg|\.png|\.jpeg]))[\'|\"].*?[\/]?>/";
        preg_match_all($pattern,$content,$result);
        return reset(end($result));
    }
}

if(!function_exists('show_content'))
{
    function show_content($content, $pageNumber = 200)
    {
        $content = htmlspecialchars_decode(str_replace(' ', '', $content));
        $content = strip_tags($content);
        if(mb_strlen($content) > $pageNumber){
            $result = mb_substr($content, 0, $pageNumber) . '...';
        }else{
            $result = $content;
        }
        return trim($result);
    }
}

if(!function_exists('pagination'))
{
    function pagination($currentPageNumber = 1, $pageSizeNumber = 10, $dataCountNumber = 0, $param = [])
    {
        if(empty($currentPageNumber)){
            $currentPageNumber = \Core\Http\Request::instance()->param('currentPage');
        }
        $page = new \Core\Tool\Page($currentPageNumber, $pageSizeNumber, $dataCountNumber, $param);
        return $page->getPageHtml();
    }
}

if(!function_exists('get_detail_route'))
{
    function get_detail_route($path)
    {
        $result = 'Blog/Article/detail';
        if(is_string($path) && $path){
            preg_match('/\d+/', $path, $match);
            $_GET['articleId'] = isset($match[0]) ? $match[0] : 0;
        }
        return $result;
    }
}

