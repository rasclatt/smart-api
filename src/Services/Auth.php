<?php
namespace Nubersoft\Api\Services;

use \Nubersoft\Api\Dto\Services\Auth\GetUserTokenRequest;

class Auth extends \Nubersoft\Api\Init
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
            throw new \Nubersoft\Api\Exception('Member/Distributor Id is required.', 500);

        return [
            'jwt_token' => $this->JWT->create([
                'Member' => $request->distid
            ])
        ];
    }
}