<?php
namespace SmartApi;

class Exception extends \Exception
{
    /**
     *	@description	
     *	@param	
     */
    public function halt()
    {
        \Nubersoft\nApp::call()->ajaxResponse([
            'error' => $this->getMessage(),
            'code' => $this->getCode()
        ]);
    }
}