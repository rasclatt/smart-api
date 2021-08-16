<?php
namespace Nubersoft\Api\Dto\App\Init;

class Request extends \SmartDto\Dto
{
    public $class_name = '';
    public $class = '';
    public $method = '';
    /**
     *	@description	
     *	@param	
     */
    protected function beforeConstruct($array)
    {
        $service = ($array['service'])?? false;
        
        if(strpos($service, '.') === false)
            throw new \Nubersoft\Api\Exception('Invalid service', 500);

        $service = array_filter(array_map(function($v) {
            return preg_replace('/[^A-Z]/i', '', $v);
        }, explode('.', $service)));
        
        if(count($service) != 2)
            throw new \Nubersoft\Api\Exception('Invalid service', 500);

        $array['class_name'] = $service[0];
        $array['class'] = "\\Nubersoft\\Api\\Services\\{$service[0]}";
        $array['method'] = $service[1];

        return $array;
    }
}