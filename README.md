# PHP Rest API

## How to install?

Once Apache and MySQL is ready, deploy the site to localhost (or server) and update the values for following parameters in index.php.

```php
$config = array(
    /** MySQL database name */
    'database_name' => '',
    /** MySQL hostname */
    'database_host' => '',
    /** MySQL database username */
    'database_user' => '',
    /** MySQL database password */
    'database_password' => '',
);
```

After setting values for the above parameters, visit the URL http://servername/phprestapi/config/install.php. install.php will create the database and tables.

If there is no database exists, install.php will create the database and tables. Otherwise, displays the message "Database exists!".

## APIs

### Create Product
Method to create single product. 

##### POST http://servername/phprestapi/product

##### BODY PARAMS

name (string) - Product name.
```json
{
	"name": "value"
}
```

##### Returns
Returns a product object if the call succeeded. 

```json
{
	"status": 201,
	"message": "New product was created.",
	"data": {
		"id": "1",
		"name": "Product 1",
		"created_date": "2019-10-16 15:30:17"
	}
}
```

**Error responses**

1. Product with same name exists
```json
{
	"status": 200,
	"message": "Product already exist.",
	"data": "Product 1"
 }
```

2. Invalid input parameters
```json
{
	"status": 400,
	"message": "Unable to create product. Required data is incomplete.",
	"data": null
}
```

### Create Purchaser
Method to create single purchaser.

##### POST http://servername/phprestapi/purchaser

##### BODY PARAMS

name (string) - Purchaser name.

```json
{
	"name": "value"
}
```

##### Returns
Returns a purchaser object if the call succeeded.

```json
{
	"status": 201,
	"message": "New purchaser was created.",
	"data": {
		"id": "1",
		"name": "Purchaser 1",
		"created_date": "2019-10-16 15:50:28"
	}
}
```

**Error responses**

1. Purchaser with same name exists
```json
{
	"status": 200,
	"message": "Purchaser already exist.",
	"data": "Purchaser 1"
}
```

2. Invalid input parameters
```json
{
	"status": 400,
	"message": "Unable to create purchaser. Required data is incomplete.",
	"data": null
}
```

### Create Purchaser
Method to create single purchase.

##### POST http://servername/phprestapi/purchaser-product

##### BODY PARAMS

purchaser_id (integer) - Purchaser ID.
product_id (integer) - Product ID.
purchase_timestamp (string) - Purchase time-stamp.

```json
{
	"purchaser_id": 1,
	"product_id": 1,
	"purchase_timestamp": "1570924800"
}
```

##### Returns
Returns a purchase object if the call succeeded.

```json
{
	"status": 201,
	"message": "New purchase was created.",
	"data": {
		"id": "1",
		"purchaser_id": "1",
		"product_id": "1",
		"purchase_timestamp": "1570924800",
		"created_date": "2019-10-16 16:03:28"
	}
}
```

**Error responses**

1. Purchase already exists
```json
{
	"status": 200,
	"message": "Purchase already exist.",
	"data": null
}
```

2. Invalid input parameters
```json
{
	"status": 400,
	"message": "Unable to create purchase. Required data is incomplete.",
	"data": null
}
```

3. Invalid Purchaser ID
```json
 {
	"status": 200,
	"message": "Purchaser not found with id",
	"data": 2
}
```

4. Invalid Product ID
```json
 {
	"status": 200,
	"message": "Product not found with id",
	"data": 2
}
```

### Get Purchase records
Method to read all purchase records which matches the input parameters.

##### GET http://servername/phprestapi/purchaser/{purchaser_id}/product?start_date={$start_date}&end_date={$end_date}

##### PATH PARAMS

purchaser_id (int) - As identifier that identifies the purchaser.

##### URL PARAMS
start_date (string) - From date in  YYYY-MM-DD format. It is optional.
end_date (string) - To date in  YYYY-MM-DD format. It is optional.

##### Returns
Returns purchase records which matches the input parameters.
```json
{
	"purchases": [
		{
			"2019-10-16": [
				{
					"product": "Bus"
				},
				{
					"product": "Car"
				},
				{
					"product": "jeep"
				},
				{
					"product": "Motor Cycle"
				}
			],
			"2019-10-13": [
				{
					"product": "Product 1"
				},
				{
					"product": "Product 2"
				},
				{
					"product": "Product 3"
				}
			]
		}
	]
}
```

**Error responses**

1. Purchase records does not exist for the purchaser
```json
{
	"status": 200,
	"message": "No purchase(s) found which matches the given criteria.",
	"data": null
	}
```

2. Purchase does not exist for the given purchaser ID
```json
{
	"status": 200,
	"message": "Purchaser not found with id",
	"data": 2
}
```

3. Purchaser ID is invalid (for example 23XEX)
```json
{
	"status": 400,
	"message": "Purchaser ID is invalid.",
	"data": "23XEX"
}
```

4. Start date is invalid (for example 2019-13-3)
```json
{
	"status": 400,
	"message": "Start date is invalid.",
	"data": "2019-13-3"
}
```

5. End date is invalid (for example 2019-12-34)
```json
{
	"status": 400,
	"message": "End date is invalid.",
	"data": "2019-12-34"
}
```

6. Start date is greater than End date is invalid
```json
{
	"status": 400,
	"message": "Start date is greater than end date.",
	"data": {
		"start_date": "2019-12-3",
		"end_date": "2019-09-20"
		}
}
```
