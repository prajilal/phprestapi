<?php

include_once 'Route.php';
include_once 'models/Purchaser.php';

/**
 * Class PurchaserController.
 */
class PurchaserController extends Route
{
    /**
     * Get the details of a purchaser with respect to the parameter
     */
    public function getPurchaser()
    {
        // TODO
    }

    /**
     * Get the details of all purchasers
     */
    public function getAllPurchasers($req)
    {
        // TODO
    }

    /**
     * Create new purchaser with the posted data ('name')
     */
    public function createPurchaser()
    {
        $purchaser = new Purchaser($this->dbConnection);

        // get posted data
        $data = json_decode(file_get_contents("php://input"));

        // make sure 'name' is not empty
        if (!empty($data->name) && Validate::isString($data->name)) {

            // read the details of purchaser with the name (duplicate check)
            $args_arr = array(
                array("key" => "name", "value" => $data->name)
            );
            $purchaser->readOne($args_arr);

            // if the purchaser exist, tell user that duplicates are not allowed
            if ($purchaser->name != null) {
                $this->response(200, Constants::PURCHASER_ALREADY_EXIST, $purchaser->name);
            }
            // if there is no purchaser exist, create new
            else {
                $purchaser->name = $data->name;
                $ret = $purchaser->create();

                if ($ret != 0) {

                    // Read the purchaser
                    $args_arr = array(
                        array("key" => "id", "value" => $ret)
                    );

                    $purchaser->readOne($args_arr);
                    $this->response(201, Constants::PURCHASER_CREATED, $purchaser);
                } else {
                    $this->response(503, Constants::UNABLE_TO_CREATE_PURCHASER_503, NULL);
                }
            }
        } else {
            $this->response(400, Constants::UNABLE_TO_CREATE_PURCHASER, NULL);
        }
    }
}
