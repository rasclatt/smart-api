<?php
namespace SmartApi\Dto\App\InitRest;

class Request extends \SmartDto\Dto
{
    public string $class_name = '';
    public string $class = '';
    public string $method = '';
    public string $dto_path = '';
    public $dto;
    /**
     *	@description	
     *	@param	
     */
    protected function beforeConstruct($array)
    {
        # Fetch controllers
        $filters = \SmartApi\App::$controllers;
        # Set the Dto
        $dto = null;
        # Loop filter if they exist
        foreach ($filters as $filter) {
            # Set the path/regex
            $reg = $filter['path'];
            # See if there is a routing match
            if(preg_match("!^{$reg}!", \SmartApi\App::$dto->service, $match)) {
                # Process the callback
                $dto = (is_callable($filter['callback']))? $filter['callback']($match[0]) : [];
                # Convert the DTO if the return is a SmartDto
                if($dto instanceof \SmartDto\Dto)
                    $dto = $dto->toArray();
                # Store the focus of the call
                \SmartApi\App::$dto->focus = $dto;
                $array['dto'] = \SmartApi\App::$dto->toArray();
                $exp = explode('\\', $filter['object']);
                $array['class_name'] = array_pop($exp);
                $array['class'] = $filter['object'];
                $array['method'] = \SmartApi\App::$dto->action;
                $array['dto_path'] = ((!empty($filter['dto']))? $filter['dto'] : $filter['object']).'\\'.ucfirst($array['method']);
                return $array;
                break;
            }
        }
        return [];
    }
}