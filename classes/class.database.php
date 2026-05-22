<?php

class Database
{
    private ?PDO $_connection = null;
    private static ?Database $_instance = null;
    private string $_host;
    private string $_username;
    private string $_password;
    private string $_database;

    public static function getInstance(): Database
    {
        if (!self::$_instance) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    private function __construct()
    {
        $this->_host     = DB_HOST;
        $this->_username = DB_USERNAME;
        $this->_password = DB_PASSWORD;
        $this->_database = DB_NAME;

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

            // Aligne la timezone MySQL sur Europe/Paris pour que current_timestamp(),
            // NOW(), CURDATE() etc. produisent l'heure locale française.
            // On utilise l'offset (+02:00 / +01:00) plutôt que 'Europe/Paris'
            // car les tables de fuseaux MySQL ne sont pas toujours installées.
            $offset = (new DateTime('now', new DateTimeZone('Europe/Paris')))->format('P');
            $this->_connection->exec("SET time_zone = '{$offset}'");
        } catch (PDOException $e) {
            trigger_error("Failed to connect to MySQL: " . $e->getMessage(), E_USER_ERROR);
        }
    }

    private function __clone() {}

    // Get PDO connection
    public function getConnection(): ?PDO
    {
        return $this->_connection;
    }
}
