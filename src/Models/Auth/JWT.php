<?php
namespace Nubersoft\Api\Models\Auth;

use \Nubersoft\Api\Interfaces\IAuth;
use \Nubersoft\JWTFactory;

use \Nubersoft\Api\Dto\ {
    Auth\Validate
};

class JWT extends \Nubersoft\Api\Models\Auth
{
    public static $jwt;
    /**
     *	@description	
     *	@param	
     */
    public function __construct(
        Validate $Validate
    )
    {
        $this->Validate = $Validate;
    }
    /**
     *	@description	
     *	@param	
     */
    public function validate(): bool
    {
        try {
            # Fetch the JWT and set the default attributes
            $Jwt = JWTFactory::get();
            $Jwt->setAttr('aud', 'api.backoffice')
                ->setAttr('iss', 'api.beyond');
            # Store the token data
            self::$jwt = 
            $data = $Jwt->get($this->Validate->apikey);
            # If from the back office directly
            if(!empty($data['unique_name'])) {
                \Nubersoft\Api\App::$request['distid'] = ($data['sub'])?? null;
                if(!\Nubersoft\Api\App::$request['distid'])
                    return false;
                \Nubersoft\Api\App::$request['distid']    =   (int) \Nubersoft\Api\App::$request['distid'];
                return true;
            }
            # See if it's a member call
            else {
                \Nubersoft\Api\App::$request['distid'] = ($data['Member'])?? null;
                return (!empty(\Nubersoft\Api\App::$request['distid']));
            }
        }
        catch (\Exception $e) {
            throw new \Nubersoft\Api\Exception('Token is invalid', 403);
        }
    }
}