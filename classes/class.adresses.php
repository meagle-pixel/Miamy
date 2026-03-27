<?php

	function getAdress($id)
	{
		$terrains = array();
		$db = Database::getInstance();
		$mysqli = $db->getConnection(); 
		$mysqli->set_charset( 'utf8');
		$query = "SELECT * FROM `adresses` WHERE id='".$id."'";
		
		if($result = $mysqli->query($query)){
			while ($message = $result->fetch_assoc()) { $terrains = $message; }
			$result->free();
		}

		return $terrains;
	}
	
	function insertAdress($adresse)
	{
		$db = Database::getInstance();
		$mysqli = $db->getConnection(); 
		$query = "INSERT INTO `adresses` (`id`,`libelle`,`adresse`, `adresse_comp`, `codepostal`, `ville`) 
		 VALUES (NULL, '".$adresse['libelle']."', '".$adresse['adresse']."','".$adresse['adresse_comp']."', '".$adresse['codepostal']."','".$adresse['ville']."');";
		
		$mysqli->query($query);
		
		return $mysqli->insert_id;
	}
	
	function updateAdress($adresse)
	{
		$db = Database::getInstance();
		$mysqli = $db->getConnection(); 
		$query = "UPDATE `adresses` SET `libelle` = '".$adresse['libelle']."',
		`adresse` = '".$adresse['adresse']."',
		`adresse_comp` = '".$adresse['adresse_comp']."',
		`codepostal` = '".$adresse['codepostal']."',
		`ville` = '".$adresse['ville']."'
		WHERE `id` = '".$adresse['id']."';";
		
		$mysqli->query($query);
	}
	
	function deleteAdresses($ids)
	{
		$adresses = explode(',',$ids);
		
		$db = Database::getInstance();
		$mysqli = $db->getConnection(); 
		$query = "DELETE FROM `adresses` 
		WHERE `id` = '".$adresses[0]."';";
		
		$mysqli->query($query);
		
		if(isset($adresses[1]))
		{
			$query = "DELETE FROM `adresses` 
			WHERE `id` = '".$adresses[1]."';";
			
			$mysqli->query($query);
		}
	}
?>