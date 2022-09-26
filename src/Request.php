<?php
namespace SmartApi;

use \SmartApi\Dto\Request\Input\Constructor;

class Request
{
    private static Constructor $dto;
    /**
     *	@description	
     *	@param	
     */
    public function __construct(Constructor $dto)
    {
        self::$dto = $dto;
    }
    /**
     *	@description	
     *	@param	
     */
    public static function get(): Constructor
    {
        return self::$dto;
    }
}