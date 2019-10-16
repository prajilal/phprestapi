<?php

include_once 'util/Constants.php';
include_once 'util/Validate.php';
include_once 'base/BaseResponse.php';
include_once 'config/DatabaseConnector.php';

/**
 * Class Route
 */
class Route extends BaseResponse
{
    /** @var  $dbConnection Database connection link*/
    protected $dbConnection = null;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $databaseConnector = new DatabaseConnector();
        $this->dbConnection = $databaseConnector->connect(_DB_SERVER_, _DB_USER_, _DB_PASSWD_, _DB_NAME_);
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
        parent::response($status, $status_message, $data);
    }
}
