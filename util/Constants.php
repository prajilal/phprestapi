<?php

/**
 * Class Constants
 */
class Constants
{
    const INVALID_PRODUCT_ID = "Product ID is invalid.";
    const INVALID_PURCHASER_ID = "Purchaser ID is invalid.";
    const INVALID_PURCHASER_TIMESTAMP = "Invalid purchase timestamp";

    const PRODUCT_NOT_EXIST = "Product not found with id";
    const PURCHASER_NOT_EXIST = "Purchaser not found with id";

    const PRODUCT_CREATED = "New product was created.";
    const PRODUCT_ALREADY_EXIST = "Product already exist.";
    const UNABLE_TO_CREATE_PRODUCT = "Unable to create product. Required data is incomplete.";
    const UNABLE_TO_CREATE_PRODUCT_503 = "Unable to create product.";

    const PURCHASE_CREATED = "New purchase was created.";
    const PURCHASE_ALREADY_EXIST = "Purchase already exist.";
    const UNABLE_TO_CREATE_PURCHASE = "Unable to create purchase. Required data is incomplete.";
    const UNABLE_TO_CREATE_PURCHASE_503 = "Unable to create purchase.";

    const PURCHASER_CREATED = "New purchaser was created.";
    const PURCHASER_ALREADY_EXIST = "Purchaser already exist.";
    const UNABLE_TO_CREATE_PURCHASER = "Unable to create purchaser. Required data is incomplete.";
    const UNABLE_TO_CREATE_PURCHASER_503 = "Unable to create purchaser.";

    const INVALID_END_DATE = "End date is invalid.";
    const INVALID_START_DATE = "Start date is invalid.";
    const INVALID_START_END_DATE = "Start date is greater than end date.";
    const NO_PURCHASE_RECORDS = "No purchase(s) found which matches the given criteria.";
}
