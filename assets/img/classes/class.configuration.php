<?php
	
	function getConfigurationById($id)
	{
		$sets = array();
		$db = Database::getInstance();
		$mysqli = $db->getConnection(); 
		$mysqli->set_charset( 'utf8');
		$query = "SELECT * FROM `configuration` WHERE `id` = '".$id."';";
		
		if($result = $mysqli->query($query)){
			while ($message = $result->fetch_assoc()) { $sets = $message; }
			$result->free();
		}
		
		return $sets;
	}

	function getConfigurationByName($name)
	{
		$sets = array();
		$db = Database::getInstance();
		$mysqli = $db->getConnection(); 
		$mysqli->set_charset( 'utf8');
		$query = "SELECT * FROM `configuration` WHERE `name` = '".$name."';";
		
		if($result = $mysqli->query($query)){
			while ($message = $result->fetch_assoc()) { $sets = $message; }
			$result->free();
		}
		
		return $sets['value'];
	}
	
	function getFullConfiguration()
	{
		$sets = array();
		$db = Database::getInstance();
		$mysqli = $db->getConnection(); 
		$mysqli->set_charset( 'utf8');
		$query = "SELECT * FROM `configuration` ORDER BY `order` ASC;";
		
		if($result = $mysqli->query($query)){
			while ($message = $result->fetch_assoc()) { $sets[] = $message; }
			$result->free();
		}
		
		return $sets;
	}
	
	
	

?>