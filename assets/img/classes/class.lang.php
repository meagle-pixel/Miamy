<?php
	
	function getAllLangsFromSite()
	{
		$sets = array();
		$db = Database::getInstance();
		$mysqli = $db->getConnection(); 
		$mysqli->set_charset( 'utf8');
		$query = "SELECT * FROM `langues`;";
		
		if($result = $mysqli->query($query)){
			while ($message = $result->fetch_assoc()) { $sets[] = $message; }
			$result->free();
		}
		
		return $sets;
	}
	
	function getAllLangs()
	{
		$sets = array();
		$db = Database::getInstance();
		$mysqli = $db->getConnection(); 
		$mysqli->set_charset( 'utf8');
		$query = "SELECT * FROM `pokemon-languages`;";
		
		if($result = $mysqli->query($query)){
			while ($message = $result->fetch_assoc()) { $sets[] = $message; }
			$result->free();
		}
		
		return $sets;
	}
	
	function getLang($id)
	{
		$sets = array();
		$db = Database::getInstance();
		$mysqli = $db->getConnection(); 
		$mysqli->set_charset( 'utf8');
		$query = "SELECT * FROM `pokemon-languages` WHERE `id` = '".$id."';";
		
		if($result = $mysqli->query($query)){
			while ($message = $result->fetch_assoc()) { $sets = $message; }
			$result->free();
		}
		
		return $sets;
	}
	
	function getLangFromSite($id)
	{
		$sets = array();
		$db = Database::getInstance();
		$mysqli = $db->getConnection(); 
		$mysqli->set_charset( 'utf8');
		$query = "SELECT * FROM `langues` WHERE `id` = '".$id."';";
		
		if($result = $mysqli->query($query)){
			while ($message = $result->fetch_assoc()) { $sets = $message; }
			$result->free();
		}
		
		return $sets;
	}
	
	function getLangFromSiteForCardLangId($id)
	{
		$sets = array();
		$db = Database::getInstance();
		$mysqli = $db->getConnection(); 
		$mysqli->set_charset( 'utf8');
		$query = "SELECT * FROM `langues` WHERE `equi_card` = '".$id."';";
		
		if($result = $mysqli->query($query)){
			while ($message = $result->fetch_assoc()) { $sets = $message; }
			$result->free();
		}
		
		return $sets;
	}
?>