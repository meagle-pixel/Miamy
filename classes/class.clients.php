<?php

	function getAllClients($onlynb = false)
	{
		$etats = array();
		$db = Database::getInstance();
		$mysqli = $db->getConnection(); 
		$mysqli->set_charset( 'utf8');
		$query = "SELECT * FROM `clients`";
		
		if($result = $mysqli->query($query)){
			while ($societe = $result->fetch_assoc()) { $etats[] = $societe; }
			$result->free();
		}
		if($onlynb)
			return count($etats);
		return $etats;
	}
	
	function deleteClient($id)
	{
		$db = Database::getInstance();
		$mysqli = $db->getConnection(); 
		$query = "DELETE FROM `clients` 
		 WHERE `id` = '".$id."';";
		
		return $mysqli->query($query);
	}
	
	function getClient($id)
	{
		$client = array();
		$db = Database::getInstance();
		$mysqli = $db->getConnection(); 
		$mysqli->set_charset( 'utf8');
		$query = "SELECT * FROM `clients` WHERE `id` = '".$id."';";
		
		if($result = $mysqli->query($query)){
			while ($message = $result->fetch_assoc()) { $client[] = $message; }
			$result->free();
		}
		
		return $client[0];
	}
	
	function getClientByEmail($email)
	{
		$client = array();
		$db = Database::getInstance();
		$mysqli = $db->getConnection(); 
		$mysqli->set_charset( 'utf8');
		$query = "SELECT * FROM `clients` WHERE `email` = '".$email."';";
		
		if($result = $mysqli->query($query)){
			while ($message = $result->fetch_assoc()) { $client[] = $message; }
			$result->free();
		}
		
		return $client[0];
	}
	
	
	function insertClient($client)
	{			
		$db = Database::getInstance();
		$mysqli = $db->getConnection(); 
		$query = "INSERT INTO `clients` (`id`, `email`, `motdepasse`, `civilite`,`nom`, `prenom`, `telephone`, `adresse`, `adresse_comp`, `codepostal`, `ville`, `dateinscription`, `dateconnect`, `dateaction`) 
		VALUES (NULL, '".$client['email']."', '".password_hash($client['motdepasse'],PASSWORD_DEFAULT )."', '".$client['civilite']."',  '".$client['nom']."', '".$client['prenom']."', '".$client['telephone']."', '".$client['adresse']."', '".$client['adresse_comp']."', '".$client['codepostal']."','".$client['ville']."',NOW(), NOW(), NOW());";
		
		$mysqli->query($query);
		
		$idc = $mysqli->insert_id;
		
		return $idc;
	}
	
	function updateClientDataLite($id)
	{
		$db = Database::getInstance();
		$mysqli = $db->getConnection(); 
		$query = "SELECT * FROM `clients` WHERE `id` = '".$id."'";
		if($result = $mysqli->query($query)){
			while ($results = $result->fetch_assoc()) {
				$_SESSION['connected'] = true;
				$_SESSION['user'] = $results;
			}
			$result->free();
		}
		else
		{
			$_SESSION['connected'] = false;
			$_SESSION['user'] = false;
		}
		
		$query = "UPDATE `clients` set `dateaction` = NOW() WHERE `id` = '".$id."'";
		$mysqli->query($query);
		
		insertIP($id,2);
	}
	
	function trytoconnectClient($email,$pass)
	{
		$db = Database::getInstance();
		$mysqli = $db->getConnection(); 
		$query = "SELECT * FROM `clients` WHERE `email` = '".$email."'";
		
		if($result = $mysqli->query($query)){
			while ($results = $result->fetch_assoc()) {
				$_SESSION['connected'] = true;
				$_SESSION['user'] = $results;
			}
			$result->free();
		}
		else
		{
			$_SESSION['connected'] = false;
			$_SESSION['user'] = false;
		}
		
		if (isset($_SESSION['user']['motdepasse']) && password_verify($pass, $_SESSION['user']['motdepasse'])) {
			return true;
		} else {
			$_SESSION['connected'] = false;
			$_SESSION['user'] = false;
			return false;
		}
	}
	
	function updateClient($client)
	{		
		$db = Database::getInstance();
		$mysqli = $db->getConnection(); 
		$query = "UPDATE `clients`
		SET `email` = '".$client['email']."', 
		`civilite` = '".$client['civilite']."',
		`nom` = '".$client['nom']."', 
		`prenom` = '".$client['prenom']."',
		`telephone` = '".$client['telephone']."',
		`adresse` = '".$client['adresse']."', 
		`adresse_comp` = '".$client['adresse_comp']."',
		`codepostal` = '".$client['codepostal']."', 
		`ville` = '".$client['ville']."'
		WHERE `id` = '".$client['id']."';";
		
		$mysqli->query($query);
		
		if(isset($client['motdepasse']))
		{
			$db = Database::getInstance();
			$mysqli = $db->getConnection(); 
			$query = "UPDATE `clients`
			SET `motdepasse` = '".password_hash($client['motdepasse'],PASSWORD_DEFAULT )."'
			WHERE `id` = '".$client['id']."';";
			
			$mysqli->query($query);
		}
	}
	
	function changePassword($id,$pass)
	{
		$db = Database::getInstance();
		$mysqli = $db->getConnection(); 
		$query = "UPDATE `clients`
		SET `motdepasse` = '".password_hash($pass,PASSWORD_DEFAULT )."'
		WHERE `id` = '".$id."';";
		
		$mysqli->query($query);
	}
	
	function existEmailClient($email)
	{
		$client = '';
		$db = Database::getInstance();
		$mysqli = $db->getConnection(); 
		$mysqli->set_charset( 'utf8');
		$query = $mysqli->prepare("SELECT * FROM `clients` WHERE `email` = '".$email."'");
		$query->execute();
		$query->store_result();

		$rows = $query->num_rows;

		if($rows > 0)
			return true;
		else
			return false;
	}


?>