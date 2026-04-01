<?php
/*
* Mysql database class - only one connection alowed
*/
class Database
{
	private $_connection;
	private static $_instance; //The single instance
	private $_host;
	private $_username;
	private $_password;
	private $_database;
	/*
	Get an instance of the Database
	@return Instance
	*/
	public static function getInstance()
	{
		if (!self::$_instance) { // If no instance then make one
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	// Constructor
	private function __construct()
	{
		$this->_host = $GLOBALS["db_host"];
		$this->_username = $GLOBALS["db_username"];
		$this->_password = $GLOBALS["db_password"];
		$this->_database = $GLOBALS["db_name"];

		$this->_connection = new mysqli(
			$this->_host,
			$this->_username,
			$this->_password,
			$this->_database
		);

		// Error handling
		if (mysqli_connect_error()) {
			trigger_error(
				"Failed to connect to MySQL: " . mysqli_connect_error(),
				E_USER_ERROR
			);
		}
	}
	// Magic method clone is empty to prevent duplication of connection
	private function __clone() {}
	// Get mysqli connection
	public function getConnection()
	{
		return $this->_connection;
	}
}
