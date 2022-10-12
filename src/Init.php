<?php
namespace SmartApi;

use \SmartDto\Dto;

use \SmartApi\ {
    App
};

abstract class Init implements IInit
{
    protected $request;

    public static array $matches = [ 'local' ];
    /**
     *	@description	
     */
    public function listen(string $method, $request = null)
    {
        $this->request = $request;
        # Reflect for permissions
        $Reflect = new \ReflectionObject($this);
        # Fetch the method for it's access
        $Method = $Reflect->getMethod($method);
        # Client service
        $isClientService = $Method->isProtected();
        # Admin service
        $isAdminService = $Method->isFinal();
        # Helper/Private service
        $isInternal = $Method->isPrivate();
        # Disallow protected and final types as well as empty rolls
        if((!$isClientService && !$isAdminService) || (App::$permission->roll === null))
            throw new \SmartApi\Exception('Permission denied.', 403);
        # If user is admin, run either way
        if(App::$permission->roll <= 2) {
            return $this->{$method}($this->request);
        }
        else {
            # If the user is basic logged in member and the service is protected, run
            if(App::$permission->roll === 3 && $Method->isProtected()) {
                return $this->{$method}($this->request);
            }
        }
        # Finally throw bad permissions
        throw new Exception('Permission denied.', 403);
    }
    /**
     *	@description	Runs the final response action
     */
    public static function createResponse(App $Api, bool $rest = true): array
    {
        # Run the API
        $response = ($rest)? $Api->initRest() : $Api->init();
        # Response case
        $case = $Api->getReturnType();
        # If smart dto, allow different key return formats
        if($response instanceof Dto) {
            switch($case) {
                case('c'):
                    $data = $response->toCamelCase();
                    break;
                case('p'):
                    $data = $response->toPascalCase();
                    break;
                default:
                    $data = $response->toArray();
            }
        } else {
            $data = $response;
        }
        return $data;
    }
    /**
     *	@description	Error toggling based on the local status of the host name
     */
    public static function errorToggle(bool $report = false)
    {
        error_reporting(E_ALL);
        $code = (preg_match('/' . implode('|', self::$matches) . '/', $_SERVER['HTTP_HOST']))? 1 : 0;
        if($report)
            throw new \SmartApi\Exception("Current reporting mode is {$code} -> error reporting is ".(($code == 1)? 'on' : 'off'), 200);
        ini_set('display_errors', $code);
    }
}