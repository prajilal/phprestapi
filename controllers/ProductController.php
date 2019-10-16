<?php

include_once 'Route.php';
include_once 'models/Product.php';

/**
 * Class ProductController.
 */
class ProductController extends Route
{
    /**
     * Get the details of a product with respect to the parameter
     */
    public function getProduct()
    {
        // TODO
    }

    /**
     * Get the details of all products
     */
    public function getAllProducts()
    {
        // TODO
    }

    /**
     * Create new product with the posted data ('name')
     */
    public function createProduct()
    {
        $product = new Product($this->dbConnection);

        // get posted data
        $data = json_decode(file_get_contents("php://input"));

        // make sure 'name' is not empty
        if (!empty($data->name) && Validate::isString($data->name)) {

            // read the details of product with the name (duplicate check)
            $args_arr = array(
                array("key" => "name", "value" => $data->name)
            );
            $product->readOne($args_arr);

            // if the product exist, tell user that duplicates are not allowed
            if ($product->name != null) {
                $this->response(200, Constants::PRODUCT_ALREADY_EXIST, $product->name);
            }
            // if there is no product exist, create new
            else {
                $product->name = $data->name;
                $ret = $product->create();

                if ($ret != 0) {

                    // Read the product
                    $args_arr = array(
                        array("key" => "id", "value" => $ret)
                    );

                    $product->readOne($args_arr);
                    $this->response(201, Constants::PRODUCT_CREATED, $product);
                } else {
                    $this->response(503, Constants::UNABLE_TO_CREATE_PRODUCT_503, NULL);
                }
            }
        } else {
            $this->response(400, Constants::UNABLE_TO_CREATE_PRODUCT, NULL);
        }
    }
}
