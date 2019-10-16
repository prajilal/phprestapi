<?php

/**
 * Class BaseRequest
 */
class BaseRequest
{
    /** @var $method Request method */
    public $method;

    /** @var $path_info $_SERVER['PATH_INFO'] array */
    public $path_info;

    /** @var $query_string $_SERVER['QUERY_STRING'] array */
    public $query_string;

    /**
     * Constructor which extract PATH_INFO, REQUEST_METHOD and 
     * PATH_INFO from _SERVER object.
     */
    public function __construct()
    {
        $this->method = $_SERVER['REQUEST_METHOD'];
        if (isset($_SERVER['PATH_INFO'])) {
            $this->path_info = explode('/', $_SERVER['PATH_INFO']);
        }
        $this->parseIncomingParams();
        return true;
    }

    /**
     * Parses the incoming URL parameters and set to the variables.
     */
    private function parseIncomingParams()
    {
        $query_string = array();

        if (isset($_SERVER['QUERY_STRING'])) {
            parse_str($_SERVER['QUERY_STRING'], $query_string);
        }

        $this->query_string = $query_string;
    }
}
