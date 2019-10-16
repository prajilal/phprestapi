<?php

include_once 'DatabaseConnector.php';

try {
    $server = "localhost";
    $user = "root";
    $password = "";
    $database = "mydb";

    $dbConnector = new DatabaseConnector();

    $create_ret = $dbConnector->createDatabase($server, $user, $password, $database);
    if ($create_ret == 200) {
        echo "Database exists!";
    } else if ($create_ret == true) {
        $connection = $dbConnector->connect($server, $user, $password, $database);
        $sql = file_get_contents("data/create_tables.sql");
        $connection->exec($sql);
        $dbConnector->disconnect();
        echo "Database setup complete!";
    } else {
        echo "Database setup failed!";
    }
} catch (PDOException $e) {
    echo $e->getMessage();
}
