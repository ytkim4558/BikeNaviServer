<?php
require_once 'DB_Config.php';

class DB_Connect {
	private $conn;
	
	// Connecting to database
	public function connect() {
		
		// Connecting to mysql database
		$this->conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
		
		// return database handler
		return $this->conn;
	}
}
?>