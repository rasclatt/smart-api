<?php
function apache_request_headers()
{
    foreach($_SERVER as $key => $value) {
        $is_content = (stripos($key, 'content_') !== false);
        $is_http = (stripos($key, 'http_') !== false);
        
        if(!$is_http && !$is_content)
            continue;

        $k = strtolower(trim(($is_http)? str_replace(' ', '-', ucwords(str_replace('_', ' ', strtolower(substr($key, 5))))) : str_replace('_', '-', strtolower($key))));
        
        if($k == 'cookie') {
            $value = explode(';', urldecode($value));
            $q = array_map(function($v) {
                $arr = [];
                parse_str($v, $arr);
                
                foreach($arr as $k => $val) {
                    if(preg_match('/:|{|\[/', $val)) {
                        $json = @json_decode($val, 1);
                        $arr[$k] = (empty($json))? $val : $json;
                    }
                }
                
                return $arr;

            }, $value);
            
            $array[$k] = $q;
        }
        else
            $array[$k] = $value;
    }

    return ($array)?? [];
}
