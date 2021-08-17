<?php
namespace SmartApi\Models\Auth;

use \SmartApi\Interfaces\IAuth;
use \Nubersoft\JWTFactory;

use \SmartApi\Dto\ {
    Auth\Validate
};

class JWT extends \SmartApi\Models\Auth
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
                \SmartApi\App::$request['distid'] = ($data['sub'])?? null;
                if(!\SmartApi\App::$request['distid'])
                    return false;
                \SmartApi\App::$request['distid']    =   (int) \SmartApi\App::$request['distid'];
                return true;
            }
            # See if it's a member call
            else {
                \SmartApi\App::$request['distid'] = ($data['Member'])?? null;
                return (!empty(\SmartApi\App::$request['distid']));
            }
        }
        catch (\Exception $e) {
            return false;
        }
    }
}
