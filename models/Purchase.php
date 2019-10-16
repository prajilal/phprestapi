<?php

/**
 * Class Purchase.
 */
class Purchase
{
	/** @var $conn Database connection link */
	private $conn;

	/** @var $table_name Table name */
	private $table_name = "purchaser_product";

	/** @var $id Purchase ID */
	public $id;

	/** @var $id Purchaser ID */
	public $purchaser_id;

	/** @var $id Product ID */
	public $product_id;

	/** @var string $purchase_timestamp Purchase timestamp */
	public $purchase_timestamp;

	/** @var $created date */
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
	 * Read all records from the purchaser_product table.
	 */
	function read()
	{
		$query = "SELECT p.id, p.purchaser_id, p.product_id, p.purchase_timestamp, p.created_date FROM " . $this->table_name . " p ORDER BY p.created_date DESC";
		$stmt = $this->conn->prepare($query);
		$stmt->execute();
		return $stmt;
	}

	/**
	 * Read purchase which matches the input criteria from the purchaser_product table.
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

		$query = "SELECT p.id, p.purchaser_id, p.product_id, p.purchase_timestamp, p.created_date 
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
		$this->product_id = $row['product_id'];
		$this->purchaser_id = $row['purchaser_id'];
		$this->purchase_timestamp = $row['purchase_timestamp'];
		$this->created_date = $row['created_date'];
	}

	/**
	 * Create new purchase.
	 *
	 * @return int Last inserted record ID or 0 (zero)
	 */
	function create()
	{
		$query = "INSERT INTO " . $this->table_name . "
			SET 
				purchaser_id=:purchaser_id, 
				product_id=:product_id, 
				purchase_timestamp=:purchase_timestamp";

		try {
			$stmt = $this->conn->prepare($query);
			$stmt->bindParam(":purchaser_id", $this->purchaser_id);
			$stmt->bindParam(":product_id", $this->product_id);
			$stmt->bindParam(":purchase_timestamp", $this->purchase_timestamp);

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
