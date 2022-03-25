<?php
namespace SmartApi;

abstract class Init
{
    protected $request;
    /**
     *	@description	
     *	@param	
     */
    public function listen(string $method, $request = null)
    {
        $this->request = $request;

        $Reflect = new \ReflectionObject($this);
        # Fetch the method for it's access
        $Method = $Reflect->getMethod($method);
        # Disallow protected and final types
        if(!$Method->isProtected() || $Method->isFinal())
            throw new \SmartApi\Exception('Service is not reachable.', 403);

        return $this->{$method}($this->request);
    }
}
