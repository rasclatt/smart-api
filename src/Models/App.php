<?php
namespace SmartApi\Models;

class App implements \SmartApi\Interfaces\App\ISetUp
{
    public $headers, $contentType, $request;

    public function execute($request)
    {
        if(!function_exists('apache_request_headers'))
            include_once(__DIR__.DS.'..'.DS.'functions'.DS.'apache_request_headers.php');
        $this->request = $request;
        $this->headers = array_change_key_case(apache_request_headers(), CASE_LOWER);
        $this->contentType = ($this->headers['content-type'])?? 'application/json';

        if(is_string($this->request)) {
            # Decode to array with json
            if(strpos($this->contentType, 'json') !== false)
                $this->request   =   json_decode(\Nubersoft\nApp::call()->dec($this->request), 1);
            # Parse query string
            else {
                $arr    =   [];
                parse_str($this->request, $arr);
                $this->request   =   $arr;
            }
        }
        
        $headerResponse    =   [
            # Set the header for response
            'Content-type' => 'application/json',
            # Set reply header
            "Access-Control-Allow-Origin" => "*",
            "Content-Type" => ((!empty($this->request['format']) && $this->request['format'] == 'csv')? 'text/csv' : 'application/json')."; charset=UTF-8",
            "Access-Control-Allow-Methods" => "POST,GET,PATCH,PUT,DELETE",
            "Access-Control-Max-Age" => "3600",
            "Access-Control-Allow-Headers" => "Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With",
            'X-Powered-By'=>'PHP/5.2.3'
        ];
        # Set the time out
        ini_set('max_execution_time', 3000000000);
        # Set all the response headers
        foreach($headerResponse as $attr => $value)
            header("{$attr}: {$value}");

        return $this->request;
    }
}