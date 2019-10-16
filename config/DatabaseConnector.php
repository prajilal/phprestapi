<?php

/**
 * Class DatabaseConnector
 */
class DatabaseConnector
{
    /** @var PDO Database connection */
    private $connection = null;

    /**
     * Constructor
     */
    public function __construct()
    { }

    /**
     * Returns a new PDO object (database connection)
     *
     * @param string $host
     * @param string $user
     * @param string $password
     * @param string $dbname
     * @param int $timeout
     * @return PDO
     */
    protected static function getPDO($host, $user, $password, $dbname, $timeout = 5)
    {
        $dsn = 'mysql:';
        if ($dbname) {
            $dsn .= 'dbname=' . $dbname . ';';
        }
        if (preg_match('/^(.*):([0-9]+)$/', $host, $matches)) {
            $dsn .= 'host=' . $matches[1] . ';port=' . $matches[2];
        } elseif (preg_match('#^.*:(/.*)$#', $host, $matches)) {
            $dsn .= 'unix_socket=' . $matches[1];
        } else {
            $dsn .= 'host=' . $host;
        }
        $dsn .= ';charset=utf8';

        return new PDO($dsn, $user, $password, array(PDO::ATTR_TIMEOUT => $timeout, PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true));
    }

    /**
     * Tries to connect and create a new database
     *
     * @param string $server
     * @param string $user
     * @param string $password
     * @param string $dbname
     * @return bool|int
     * @throws \Exception
     */
    public static function createDatabase($server, $user, $password, $dbname)
    {
        try {
            $connection = new PDO("mysql:" . $server, $user, $password);

            // Check DB existence
            $query = "SELECT COUNT(*) AS `exists` FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMATA.SCHEMA_NAME 
                = \"" . str_replace("`", '\\"', $dbname) . "\"";

            // execute the statement
            $mysqli = new mysqli($server, $user, $password);
            $query =  $mysqli->query($query);
            if ($query === false) {
                throw new Exception($mysqli->error, $mysqli->errno);
            }

            // extract the value
            $row = $query->fetch_object();
            $dbExists = (bool) $row->exists;

            // Create database if not exits
            if (!isset($dbExists) || $dbExists == 0) {
                $success = $connection->exec('CREATE DATABASE `' . str_replace('`', '\\`', $dbname) . '`');

                $connection->exec('SET SESSION sql_mode = \'\'');
                $connection->exec('SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO"');
                $connection->exec('SET AUTOCOMMIT = 0');
                $connection->exec('START TRANSACTION');
                $connection->exec('SET time_zone = "+09:00"');
            } else {
                return 200;
            }
        } catch (PDOException $e) {
            throw new \Exception('Failed to create database: ' . $e->getMessage());
        }

        return $success;
    }

    /**
     * Tries to connect to the database
     *
     * @param string $server
     * @param string $user
     * @param string $password
     * @param string $dbname
     * @return PDO
     * @throws \Exception
     */
    public function connect($server, $user, $password, $database)
    {
        try {
            $this->connection = $this->getPDO($server, $user, $password, $database, 5);
        } catch (PDOException $e) {
            throw new \Exception('Connection to database cannot be established: ' . $e->getMessage());
        }

        $this->connection->exec('SET SESSION sql_mode = \'\'');

        return $this->connection;
    }

    /**
     * Destroys the database connection
     */
    public function disconnect()
    {
        unset($this->connection);
    }

    /**
     * Executes an SQL statement, returning a result set as a PDOStatement object or true/false.
     *
     * @param string $sql
     * @return PDOStatement
     */
    protected function _query($sql)
    {
        return $this->connection->query($sql);
    }
}
