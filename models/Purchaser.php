<?php

/**
 * Class Purchaser.
 */
class Purchaser
{
	/** @var $conn Database connection link */
	private $conn;

	/** @var $table_name Table name */
	private $table_name = "purchaser";

	/** @var $id Purchaser ID */
	public $id;

	/** @var string $name Purchaser Name*/
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
	 * Read all records from the purchaser table.
	 */
	function read()
	{
		$query = "SELECT p.id, p.name, p.created_date FROM " . $this->table_name . " p ORDER BY p.created_date DESC";
		$stmt = $this->conn->prepare($query);
		$stmt->execute();
		return $stmt;
	}

	/**
	 * Read purchaser which matches the input criteria from the purchaser table.
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

		$query = "SELECT  p.id, p.name, p.created_date FROM " . $this->table_name . " p
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
	 * Create new purchaser.
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
