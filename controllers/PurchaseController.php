<?php

include_once 'Route.php';
include_once 'models/Purchase.php';

/**
 * Class PurchaseController.
 */
class PurchaseController extends Route
{
    /**
     * Get the details of a purchase with respect to the parameter
     */
    public function getPurchase()
    {
        // TODO
    }

    /**
     * Get the details of all purchases
     * Retrieve the purchase records with respect to the given purchaser id,
     * start date (optional) and end date (optional)
     *
     * @param BaseRequest $req Request Object
     */
    public function getAllPurchases($req)
    {
        $product = new Product($this->dbConnection);
        $purchaser = new Purchaser($this->dbConnection);

        // get purchaser id from path info 
        // assuming the second parameter of $req->path_info is purchaser id.
        // if purchaser id cannot retrieve, output invalid purchaser id error message.
        $purchaser_id = "";
        try {
            if (isset($req->path_info[2]) && !empty($req->path_info[2])) {
                if (Validate::isInt($req->path_info[2])) {
                    $purchaser_id = intval($req->path_info[2]);
                }
            }
        } catch (Exception $exception) {
            // writeToLog($exception);
        }

        if (empty($purchaser_id)) {
            $this->response(400, Constants::INVALID_PURCHASER_ID, $req->path_info[2]);
            return;
        } else {
            // Check for purchaser record with the ID.
            $args_arr = array(
                array("key" => "id", "value" => $purchaser_id)
            );

            $purchaser->readOne($args_arr);

            if ($purchaser->name == null) {
                $this->response(200, Constants::PURCHASER_NOT_EXIST, $purchaser_id);
                return;
            }
        }

        $end_date = "";
        $start_date = "";

        // Set the start_date if value of query_string['start_date'] is a date
        if (isset($req->query_string['start_date']) && !empty($req->query_string['start_date'])) {
            if (Validate::isDate($req->query_string['start_date'], 'Y-m-d') == true) {
                $start_date = $req->query_string['start_date'];
            } else {
                $this->response(400, Constants::INVALID_START_DATE, $req->query_string['start_date']);
                return;
            }
        }

        // Set the end_date if value of query_string['end_date'] is a date
        if (isset($req->query_string['end_date']) && !empty($req->query_string['end_date'])) {
            if (Validate::isDate($req->query_string['end_date'], 'Y-m-d')) {
                $end_date = $req->query_string['end_date'];
            } else {
                $this->response(400, Constants::INVALID_END_DATE, $req->query_string['end_date']);
                return;
            }
        }

        // Check if start_date is greater than end_date (when the values are set)
        if ((isset($start_date) && !empty($start_date))
            && (isset($end_date) && !empty($end_date))
        ) {
            try {
                if (
                    DateTime::createFromFormat('Y-m-d', $end_date) <
                    DateTime::createFromFormat('Y-m-d', $start_date)
                ) {
                    http_response_code(400);
                    $this->response(
                        400,
                        Constants::INVALID_START_END_DATE,
                        array(
                            "start_date" => $req->query_string['start_date'],
                            "end_date" => $req->query_string['end_date']
                        )
                    );
                    return;
                }
            } catch (Exception $ex) {
                $end_date = "";
                $start_date = "";
            }
        }

        // read the details of purchase records with the purchaser id, start date (optional)
        // and end date (optional)
        $stmt =  $product->search($purchaser_id, $start_date, $end_date);

        // check if more than 0 record found
        if ($stmt->rowCount() > 0) {
            $purchase_arr = array();

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                extract($row);
                $purchase_item = array(
                    "timestamp" => $row['purchase_timestamp'],
                    "product" => $row['name']
                );

                array_push($purchase_arr, $purchase_item);
            }

            // Grouping in to timestamp
            foreach ($purchase_arr as $array) {
                $grouped_purchase_arr[$array["timestamp"]][] = array("product" => $array["product"]);
            }

            // push data in to purchases group
            $purchases["purchases"] = array();
            array_push($purchases["purchases"], $grouped_purchase_arr);

            http_response_code(200);
            echo json_encode($purchases);
        } else {
            $this->response(200, Constants::NO_PURCHASE_RECORDS, NULL);
        }
    }

    /**
     * Create new purchase with the posted data ('name')
     */
    public function createPurchase()
    {
        // get posted data
        $data = json_decode(file_get_contents("php://input"));

        // make sure 'purchaser_id', 'product_id' & 'purchase_timestamp' is not empty
        if (
            (!empty($data->purchaser_id) && Validate::isInt($data->purchaser_id))
            && (!empty($data->product_id) && Validate::isInt($data->product_id))
            && (!empty($data->purchase_timestamp && Validate::isString($data->purchase_timestamp)))
        ) {
            $product = new Product($this->dbConnection);
            $purchase = new purchase($this->dbConnection);
            $purchaser = new Purchaser($this->dbConnection);

            // Check for product and purchaser existence
            // read the details of purchaser with the ID
            $args_arr = array(
                array("key" => "id", "value" => $data->purchaser_id)
            );
            $purchaser->readOne($args_arr);

            // read the details of product with the ID
            $args_arr = array(
                array("key" => "id", "value" => $data->product_id)
            );
            $product->readOne($args_arr);

            // If the purchaser and product does not exist, inform user the error
            if ($purchaser->name == null) {
                $this->response(200, Constants::PURCHASER_NOT_EXIST, $data->purchaser_id);
            } else if ($product->name == null) {
                $this->response(200, Constants::PRODUCT_NOT_EXIST, $data->product_id);
            } else {
                try {
                    if (Validate::isTimeStamp($data->purchase_timestamp)) {
                        $args_arr = array(
                            array("key" => "product_id", "value" => $data->product_id),
                            array("key" => "purchaser_id", "value" => $data->purchaser_id),
                            array("key" => "purchase_timestamp", "value" => $data->purchase_timestamp)
                        );

                        // read the details of purchase with the given details (duplicate check)
                        $purchase->readOne($args_arr);

                        // If the purchase exist, tell user that duplicates are not allowed
                        if ($purchase->purchase_timestamp != null) {
                            $this->response(200, Constants::PURCHASE_ALREADY_EXIST, NULL);
                        }
                        // If there is no purchase exist, create new 
                        else {
                            $purchase->product_id = $data->product_id;
                            $purchase->purchaser_id = $data->purchaser_id;
                            $purchase->purchase_timestamp = $data->purchase_timestamp;
                            $ret = $purchase->create();
            
                            if ($ret != 0) {
            
                                // Read the purchase
                                $args_arr = array(
                                    array("key" => "id", "value" => $ret)
                                );
            
                                $purchase->readOne($args_arr);
                                $this->response(201, Constants::PURCHASE_CREATED, $purchase);
                            } else {
                                $this->response(503, Constants::UNABLE_TO_CREATE_PURCHASE_503, NULL);
                            }
                        }
                    } else {
                        $this->response(400, Constants::INVALID_PURCHASER_TIMESTAMP, $data->purchase_timestamp);
                    }
                } catch (Exception $exception) {
                    // writeToLog($exception);
                    $this->response(400, Constants::INVALID_PURCHASER_TIMESTAMP, $data->purchase_timestamp);
                }
            }
        } else {
            $this->response(400, Constants::UNABLE_TO_CREATE_PURCHASE, NULL);
        }
    }
}
