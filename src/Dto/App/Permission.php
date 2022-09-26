<?php
namespace SmartApi\Dto\App;

class Permission extends \SmartDto\Dto
{
    public ?int $id;
    public ?int $role;
    public ?int $expires;

    /**
     *	@description	
     *	@param	
     */
    protected function beforeConstruct($array)
    {
        $array['expires'] = $array['iat']?? time() - 1000;
        return $array;
    }
}