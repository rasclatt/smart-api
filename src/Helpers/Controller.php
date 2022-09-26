<?php
namespace SmartApi\Helpers;

use \SmartApi\App;

class Controller
{
    /**
     *	@description	
     *	@param	
     */
    public static function add(string $obj, string $path, callable $func = null, string $dtoPath = null): void
    {
        App::$controllers[] = [
            'object' => $obj,
            'path' => $path,
            'callback' => $func,
            'dto' => $dtoPath
        ];
    }
    /**
     *	@description	
     *	@param	
     */
    public function get()
    {
        if(empty(App::$controllers))
            return null;

        foreach (App::$controllers as $filter) {
            $reg = $filter['path'];
            //die("!^{$reg}$!");
            if(preg_match("!^{$reg}$!", App::$dto->service, $match)) {
                return $filter['callback']($match[0]);
            }
        }
        return null;
    }
}