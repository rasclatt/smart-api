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

        if(!$Reflect->getMethod($method)->isProtected())
            throw new \SmartApi\Exception('Service is not reachable.', 403);

        return $this->{$method}($this->request);
    }
}
