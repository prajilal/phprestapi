<?php
// hardcode the locale as Japan
date_default_timezone_set('Asia/Tokyo');

// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$config = array(
    /** MySQL database name */
    'database_name' => 'db_name',
    /** MySQL hostname */
    'database_host' => 'localhost',
    /** MySQL database username */
    'database_user' => 'root',
    /** MySQL database password */
    'database_password' => '',
);

define('_DB_SERVER_', $config['database_host']);
define('_DB_NAME_', $config['database_name']);
define('_DB_USER_', $config['database_user']);
define('_DB_PASSWD_', $config['database_password']);

// include utilities, configurations
include_once 'base/BaseRequest.php';

include_once 'controllers/ProductController.php';
include_once 'controllers/PurchaseController.php';
include_once 'controllers/PurchaserController.php';

// instantiate request object
$request = new BaseRequest();

if (!isset($request->path_info) || sizeof($request->path_info) < 2) {
    BaseResponse::response(404, "Resource Not Found", NULL);
    die();
}

// handle each requests
switch ($request->method) {
    case "GET":
        switch ($request->path_info[1]) {
            case "purchaser":
                if (sizeof($request->path_info) > 3) {
                    switch ($request->path_info[3]) {
                        case "product":
                            // perform GET /purchaser/{$purchaser_id}/product?start_date={$start_date}&end_date={$end_date}
                            $x = new PurchaseController();
                            $x->getAllPurchases($request);
                            break;
                        default:
                            BaseResponse::response(404, "Resource Not Found", NULL);
                            break;
                    }
                } else {
                    BaseResponse::response(404, "Resource Not Found", NULL);
                }
                break;
            case "product":
            case "purchaser-product":
                BaseResponse::response(405, "The HTTP method in the request was not supported by the resource", NULL);
                break;
            default:
                BaseResponse::response(404, "Resource Not Found", NULL);
                break;
        }
        break;
    case "POST":
        if (sizeof($request->path_info) > 3) {
            BaseResponse::response(404, "Resource Not Found", NULL);
        } else {
            switch ($request->path_info[1]) {
                case "product":
                    // perform POST /product
                    $x = new ProductController();
                    $x->createProduct();
                    break;
                case "purchaser":
                    // perform POST /purchaser
                    $x = new PurchaserController();
                    $x->createPurchaser();
                    break;
                case "purchaser-product":
                    // perform POST /purchaser-product
                    $x = new PurchaseController();
                    $x->createPurchase();
                    break;
                default:
                    BaseResponse::response(404, "Resource Not Found", NULL);
                    break;
            }
        }
        break;
    default:
        BaseResponse::response(405, "The HTTP method in the request was not supported by the resource", NULL);
        break;
}
