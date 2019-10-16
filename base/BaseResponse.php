<?php

/**
 * Class BaseResponse
 */
class BaseResponse
{
    /**
     * Constructor
     */
    public function __construct()
    {
    }

    /**
     * Generate the response.
     * 
     * @param int $status the HTTP status code 
     * @param string $status_message the status message
     * @param object $data the response data
     */
    public static function response($status, $status_message, $data)
    {
        header("HTTP/1.1 " . $status);

        $response['status'] = $status;
        $response['message'] = $status_message;
        $response['data'] = $data;

        $json_response = json_encode($response, JSON_UNESCAPED_UNICODE);
        echo $json_response;
    }
}
