<?php
    require_once __DIR__ . "/setup_env.php";

    function get_db_connection() {
        $DB_HOST = $_ENV['DB_HOST'];
        $DB_NAME = $_ENV['DB_NAME'];
        $DB_CHARSET = $_ENV['DB_CHARSET'];

        $connection_config = "mysql:host=$DB_HOST;dbname=$DB_NAME;charset=$DB_CHARSET";
        $connection_options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        $pdo = new PDO($connection_config, $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD'], $connection_options);
        return $pdo;
    }