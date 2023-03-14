<?php
namespace SmartApi\Dto\Request\Input;

class Constructor extends \SmartDto\Dto
{
    public string $service;
    public string $action = 'get';
    public string $type = 'get';
    public array $body, $query = [];
    public $focus;
    /**
     *	@description	
     *	@param	
     */
    protected function beforeConstruct($array)
    {
        switch (strtolower($_SERVER['REQUEST_METHOD'])) {
            case ('post'):
                $arr['action'] = 'save';
                break;
            case ('get'):
                $arr['action'] = 'get';
                break;
            case ('patch'):
                $arr['action'] = 'update';
                break;
            case ('put'):
                $arr['action'] = 'upload';
                break;
            case ('delete'):
                $arr['action'] = 'delete';
                break;
            default:
                throw new \SmartApi\Exception('Invalid request type. Can only pass POST, GET, PATCH, PUT, and DELETE', 403);
        }
        
        $arr['body'] = [];
        $arr['service'] = rtrim($_SERVER['REDIRECT_URL']?? '', '/');
        $arr['type'] = strtolower($_SERVER['REQUEST_METHOD']?? 'get');

        if(empty($arr['service'])) {
            $exp = implode('/', array_filter(array_map(function($v) {
                return preg_replace('/[^\dA-Z_-]/i','', $v);
            }, explode('/', str_replace(['api.php', 'index.php'], ['',''],$_SERVER['PHP_SELF'])))));
            $arr['service'] = $exp;
        }
        # Process the user input data
        if(!empty($array['input'])) {
            $strArr = [];
            parse_str($array['input'], $strArr);
            $arr['body'] = $strArr;
        }
        # Process the query string attributes
        if(!empty($_SERVER['QUERY_STRING'])) {
            $strArr = [];
            parse_str($_SERVER['QUERY_STRING'], $strArr);
            if(!empty($_SERVER['REDIRECT_URL']))
                array_shift($strArr);
            $arr['query'] = $strArr;
        }
        return $arr;
    }
}
