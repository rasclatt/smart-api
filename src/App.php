<?php
namespace SmartApi;

use \SmartApi\Models\ {
    App as SetUp
};

use \SmartApi\ErrorHandler as IErrorHandler;
use \SmartApi\Dto\App\Init\Request as InitRequest;
use \Nubersoft\nReflect;

class App
{
    # Type of data keys to respond with
    public static $responseType = 'php';
    # Storage of the request
    public static $request;
    # Allow adding to headers
    public $headers;
    # Storage for the authenticator objects
    private $Auth;
    # Set an alternate core class (string)
    private static $coreFolder;
    # Set the alternate dto class (string)
    private static $coreDtoFolder;
    /**
     *	@description	Create headers for the call
     */
    public function setUp(SetUp $Setup, IErrorHandler $ErrorHandler = null): SetUp
    {
        # Convert the post
        self::$request = $Setup->execute(file_get_contents("php://input"));
        # Set the error handler
        if(!empty($ErrorHandler))
            $ErrorHandler->start();
        # Send back the setup for use
        return $Setup;
    }
    /**
     *	@description	Allow a function to run before the main init body
     *	@param	
     */
    public function beforeInit($func): App
    {
        if(is_callable($func))
            (new \Nubersoft\nReflect())->reflectFunction($func);

        return $this;
    }
    /**
     *	@description	Save an authenticator to the auth methods
     */
    public function useAuthenticator(Interfaces\IAuth ...$authenticators): App
    {
        $this->Auth = $authenticators;
        return $this;
    }
    /**
     *	@description	Main API call
     */
    public function init()
    {
        # Call is not valid by default
        $valid = false;
        # Loop through all the authenticators
        foreach ($this->Auth as $authenticator) {
            # Stop if not an authenticator
            if(!($authenticator instanceof Interfaces\IAuth)) {
                throw new Exception('Authenticator must be instance of Api\IAuth', 500);
            }
            # Stop and mark true if valid
            if($authenticator->validate()) {
                # Set to valid
                $valid = true;
                break;
            }
        }
        # Core Dto path
        $core_service = 'Api';
        # Create an init request
        $service = new InitRequest(self::$request);
        # If there is a replacement folder
        if(!empty(self::$coreFolder)) {
            $override = self::$coreFolder.'\\Services\\'.$service->class_name;
            if(class_exists($override)) {
                $obj = nReflect::instantiate($override);
                if(!method_exists($obj, $service->method)) {
                    unset($obj);
                }
                else {
                    # Overwrite the core class name
                    $service->class = $override;
                    # Overwrite the Dto folder
                    $core_service = (!empty(self::$coreDtoFolder))? self::$coreDtoFolder : $core_service;
                }
            }
        }
        # Check for valid request class
        if(!class_exists($service->class))
            throw new Exception('Invalid service', 404);
        # Create the instance
        $obj = (isset($obj))? $obj : nReflect::instantiate($service->class);
        # Break down the items to determine whether a requestd/response DTO is available
        $dtoClass = ucfirst($service->class_name);
        $dtoMethod = ucfirst($service->method);
        # Create request and response DTO paths
        $dtoStringReq = $this->classNameCompile($core_service, $dtoClass, $dtoMethod).'Request';
        $dtoStringResp = $this->classNameCompile($core_service, $dtoClass, $dtoMethod).'Response';
        # See if the DTOs are available
        $dtoExistsReq  =   class_exists($dtoStringReq);
        $dtoExistsResp  =   class_exists($dtoStringResp);
        # See if this requires authentication to access
        $isPublic = $this->isPublic($obj, $service->method);
        # Run the public function if available
        if($isPublic) {
            $run = $obj->{$service->method}($dtoExistsReq? new $dtoStringReq(self::$request) : self::$request);
        }
        else {
            # Stop if not validated to see the authenticated service
            if(!$valid) {
                throw new Exception('Invalid API credentials', 403);
            }
            # If valid but the service is not built properly
            elseif(!($obj instanceof \Api\Init)) {
                throw new Exception('Services is broken', 500);
            }
            # Run the listener for this object
            $run = $obj->listen($service->method, ($dtoExistsReq? new $dtoStringReq(self::$request) : self::$request));
        }
        # Return the response
        return ($dtoExistsResp)? new $dtoStringResp($run) : $run;
    }
    /**
     *	@description	Determines the response type of the key returns
     */
    public function getReturnType(): string
    {
        return strtolower((self::$request['rformat'])?? self::$responseType);
    }
    /**
     *	@description	Checks to see if the call is public accessible or if validation is required to view
     */
    private function isPublic(object $obj, string $method): bool
    {
        $details = new \ReflectionObject($obj);
        $methodDetails = $details->getMethod($method);
        return $methodDetails->isPublic();
    }
    /**
     *	@description	Set the path where API services are located
     */
    public static function addCoreClass(string $path): void
    {
        self::$coreFolder = $path;
    }
    /**
     *	@description	Set the core DTO folder
     */
    public static function addCoreDtoClass(string $path): void
    {
        self::$coreDtoFolder = $path;
    }
    /**
     *	@description	Format the Dto class request/return paths
     */
    private function classNameCompile(string $service, string $class, string $method): string
    {
        return '\\'.implode('\\', array_map(function($v){
            return trim($v, '\\');
        }, [
            $service,
            'Dto',
            'Services',
            $class,
            $method
        ]));
    }
}