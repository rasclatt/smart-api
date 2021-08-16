<?php
function apache_request_headers()
{
    foreach($_SERVER as $key => $value) {
        if(stripos($key, 'http_') !== false) {
            $k = str_replace(' ', '-', ucwords(str_replace('_', ' ', strtolower(substr($key, 5)))));
            $array[$k] = $value;
        }
    }

    return ($array)?? [];
}