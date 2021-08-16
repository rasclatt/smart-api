<?php
namespace Nubersoft\Api;

abstract class Init
{
    /**
     *	@description	
     *	@param	
     */
    public function listen(string $method, $request = null)
    {
        return $this->{$method}($request);
    }
}