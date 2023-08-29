<?php
namespace SmartApi\Helpers;

use \SmartApi\App;

class Controller
{
    /**
     *	@description	
     *	@important  Note that caching is not possible when the controller takes an anonymous function for the $func param. 
     */
    public static function add(
        string $obj,
        string $path,
        callable | string $func = null,
        string $dtoPath = null
    ): void
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
        # Loop controllers
        foreach (App::$controllers as $filter) {
            # Get the callback (string or func)
            $reg = $filter['path'];
            # If there is a matching route, then run it
            if(preg_match("!^{$reg}!", App::$dto->service, $match)) {
                $f = $filter['callback'];
                # If there is a string or callback function, try and run it,
                # pass in matched values from path
                if(is_string($f) || is_callable($f)) {
                    return $f($match[0]);
                }
            }
        }
        return null;
    }
}
