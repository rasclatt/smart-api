<?php
namespace SmartApi\Dto\Auth;

class Validate extends \SmartDto\Dto
{
    public $username = 0;
    public $apikey = '';
    /**
     *	@description	
     *	@param	
     */
    protected function beforeConstruct($array)
    {
        $array['username'] = trim(($array['api-user-id'])?? '');
        $array['apikey'] = str_ireplace('Bearer ', '', trim(($array['authorization'])?? ''));
        
        return $array;
    }
}