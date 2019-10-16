<?php

/**
 * Class Product.
 */
class Product
{
    /** @var $conn Database connection link */
    private $conn;

    /** @var $table_name Table name */
    private $table_name = "product";

    /** @var $id Product ID */
    public $id;

    /** @var string $name Product Name*/
    public $name;

    /** @var $created_date Created date */
    public $created_date;

    /**
     * Constructor.
     * 
     * @param $db Database connection link.
     */
    public function __construct($db)
    {
        $this->conn = $db;
    }

    /**
     * Read all records from the product table.
     */
    function read()
    {
        $query = "SELECT p.id, p.name, p.created_date FROM " . $this->table_name . " p ORDER BY p.created_date DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    /**
     * Read product which matches the input criteria from the product table.
     * 
     * @param array $args_arr search criteria array.
     */
    function readOne($args_arr)
    {
        // Construct WHERE clause
        $i = 0;
        $where = "";
        foreach ($args_arr as $item) {
            if ($i == 0) {
                $where = " p." . $item['key'] . " = ?";
            } else {
                $where .= " and p." . $item['key'] . " = ?";
            }

            $i++;
        }

        $query = "SELECT  p.id, p.name, p.created_date 
            FROM " . $this->table_name . " p 
            WHERE" . $where . "
				LIMIT
					0,1";

        $stmt = $this->conn->prepare($query);

        // bind value of parameter to be checked
        $i = 1;
        foreach ($args_arr as $item) {
            $stmt->bindParam($i, $item['value']);
            $i++;
        }

        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->id = $row['id'];
        $this->name = $row['name'];
        $this->created_date = $row['created_date'];
    }

    /**
     * Search and return products which matches the input criteria from the product table.
     *
     * @param int $purchaser_id Purchaser ID
     * @param string $start_date Start date (Optional)
     * @param string $end_date End date (Optional)
     *
     * @return object SQL Statement
     */
    function search($purchaser_id, $start_date, $end_date)
    {
        // Construct WHERE condition
        $where = " purchaser_product.purchaser_id = ?";

        if (!empty($start_date)) {
            $where .= " and DATE_FORMAT(FROM_UNIXTIME(purchaser_product.purchase_timestamp), '%Y-%m-%d') >= ?";
        }

        if (!empty($end_date)) {
            $where .= " and DATE_FORMAT(FROM_UNIXTIME(purchaser_product.purchase_timestamp), '%Y-%m-%d') <= ?";
        }

        $query = "SELECT  p.name as name, 
                DATE_FORMAT(FROM_UNIXTIME(purchaser_product.purchase_timestamp), '%Y-%m-%d')
                    AS purchase_timestamp 
                FROM " . $this->table_name . " p
                    INNER JOIN purchaser_product 
                        ON p.id = purchaser_product.product_id
                    WHERE " . $where . "
                    GROUP BY purchaser_product.purchase_timestamp DESC, p.name ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $purchaser_id);

        if (!empty($start_date)) {
            $stmt->bindParam(2, $start_date);
        }

        if (!empty($end_date)) {
            $stmt->bindParam(3, $end_date);
        }

        $stmt->execute();
        return $stmt;
    }

    /**
     * Create new product.
     *
     * @return int Last inserted record ID or 0 (zero)
     */
    function create()
    {
        $query = "INSERT INTO " . $this->table_name . " SET name=:name";

        try {
            $stmt = $this->conn->prepare($query);

            // sanitize
            $this->name = htmlspecialchars(strip_tags($this->name));
            $stmt->bindParam(":name", $this->name);

            if ($stmt->execute()) {
                return $this->conn->lastInsertId();
            }
        } catch (PDOExecption $e) {
            $stmt->rollback();
            print "Error!: " . $e->getMessage() . "</br>";
        }

        return 0;
    }
}
