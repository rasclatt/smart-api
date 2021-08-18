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
     *	@description	Setu up the validator
     */
    public function __construct(
        Validate $Validate
    )
    {
        $this->Validate = $Validate;
    }
    /**
     *	@description	Determines if a jwt token is availabe and valid
     */
    public function validate(): bool
    {
        try {
            # Fetch the JWT and set the default attributes
            $Jwt = JWTFactory::get();
            $Jwt->setAttr('aud', $_SERVER['HTTP_HOST'])
                ->setAttr('iss', $_SERVER['HTTP_HOST']);
            # Store the token data
            self::$jwt = 
            $data = $Jwt->get($this->Validate->apikey);
            \SmartApi\App::$request['member'] = ($data['member'])?? null;
            return (!empty(\SmartApi\App::$request['member']));
        }
        catch (\Exception $e) {
            return false;
        }
    }
}
