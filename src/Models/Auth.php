<?php
namespace Nubersoft\Api\Models;

use \Nubersoft\Api\ {
    Interfaces\IAuth
};

use \Nubersoft\Api\Dto\ {
    Auth\Validate
};

use \Nubersoft\nQuery;

class Auth extends \Nubersoft\nApp implements IAuth
{
    private $Query, $Validate;
    /**
     *	@description	
     *	@param	
     */
    public function __construct(
        nQuery $Query,
        Validate $Validate
    )
    {
        $this->Validate = $Validate;
        $this->Query = $Query;
    }
    /**
     *	@description	
     *	@param	
     */
    public function validate(): bool
    {
        $fetch = $this->getInternalUser();
        return isset($fetch['ID']);
    }
    /**
     *	@description	
     *	@param	
     */
    protected function getInternalUser()
    {
        return $this->Query->query("SELECT * FROM api_users WHERE user_id = ? AND apikey = ?", [
            $this->Validate->username,
            $this->Validate->apikey
        ])
            ->getResults(1);
    }
}