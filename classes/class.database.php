<?php
/*
* Database class (PDO) - only one connection allowed (Singleton)
*/
class Database
{
    private $_connection;
    private static $_instance;
    private $_host;
    private $_username;
    private $_password;
    private $_database;

    public static function getInstance()
    {
        if (!self::$_instance) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    private function __construct()
    {
        $this->_host     = $GLOBALS["db_host"];
        $this->_username = $GLOBALS["db_username"];
        $this->_password = $GLOBALS["db_password"];
        $this->_database = $GLOBALS["db_name"];

        try {
            $this->_connection = new PDO(
                "mysql:host={$this->_host};dbname={$this->_database};charset=utf8mb4",
                $this->_username,
                $this->_password,
                [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]
            );
        } catch (PDOException $e) {
            trigger_error("Failed to connect to MySQL: " . $e->getMessage(), E_USER_ERROR);
        }
    }

    private function __clone() {}

    // Get PDO connection
    public function getConnection()
    {
        return $this->_connection;
    }
}
