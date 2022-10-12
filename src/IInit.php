<?php
namespace SmartApi;

interface IInit
{
    /**
     *	@description	Main function for final execution of the API script
     */
    public function listen(string $method, $request = null);
}