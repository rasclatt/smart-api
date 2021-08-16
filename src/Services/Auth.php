<?php
namespace SmartApi\Services;

use \SmartApi\Dto\Services\Auth\GetUserTokenRequest;

class Auth extends \SmartApi\Init
{
    private $JWT;
    /**
     *	@description	
     *	@param	
     */
    public function __construct()
    {
        $this->JWT = \Nubersoft\JWTFactory::get();
    }
    /**
     *	@description	
     *	@param	
     */
    protected function getUserToken(GetUserTokenRequest $request)
    {
        if(empty($request->distid))
            throw new \SmartApi\Exception('Member/Distributor Id is required.', 500);

        return [
            'jwt_token' => $this->JWT->create([
                'Member' => $request->distid
            ])
        ];
    }
}