<?php

	/* ==========================================================================
	   GESTION DES PAGES & DROITS (ACL)
	   ========================================================================== */

	function insertPage($nom, $mod, $url)
	{
		$db = Database::getInstance();
		$mysqli = $db->getConnection();

		// Sécurisation
		$nom = $mysqli->real_escape_string($nom);
		$mod = $mysqli->real_escape_string($mod);
		$url = $mysqli->real_escape_string($url);

		$query = "INSERT INTO `pages` 
		(`id`, `nom`, `mod`, `url`) 
		VALUES 
		(NULL, '".$nom."', '".$mod."', '".$url."');";

		$mysqli->query($query);

		$idp = $mysqli->insert_id;

		// LOG : Création de page
		if($idp) {
			$userId = $_SESSION['user']['id'] ?? 0;
			if(function_exists('logUserAction')) {
				logUserAction($userId, 'create_page', "Création de la page : $nom (Module: $mod)");
			}
		}

		return $idp ? $idp : false;
	}
	
	function updatePage($id, $nom, $mod, $url)
	{
		$db = Database::getInstance();
		$mysqli = $db->getConnection();

		// Sécurisation
		$id = (int)$id;
		$nom = $mysqli->real_escape_string($nom);
		$mod = $mysqli->real_escape_string($mod);
		$url = $mysqli->real_escape_string($url);

		$query = "UPDATE `pages` 
		SET `nom` = '".$nom."', 
		`mod` = '".$mod."',
		`url` = '".$url."'
		WHERE `id` = '".$id."';";

		$res = $mysqli->query($query);

		// LOG : Modification de page
		if($res) {
			$userId = $_SESSION['user']['id'] ?? 0;
			if(function_exists('logUserAction')) {
				logUserAction($userId, 'update_page', "Modification de la page ID $id : $nom");
			}
		}

		return true;
	}

	function getPages($onlynb = false)
	{
		$autorisations = array();
		$db = Database::getInstance();
		$mysqli = $db->getConnection(); 
		$mysqli->set_charset( 'utf8');
		$query = "SELECT * FROM `pages` ORDER BY `nom` ASC";
		
		if($result = $mysqli->query($query)){
			while ($message = $result->fetch_assoc()) { $autorisations[] = $message; }
			$result->free();
		}

		if($onlynb)
			return count($autorisations);
		return $autorisations;
	}
	
	function getPageById($id)
	{
		$url = array();
		$db = Database::getInstance();
		$mysqli = $db->getConnection(); 
		$mysqli->set_charset( 'utf8');
		
		$id = (int)$id; // Sécu
		$query = "SELECT * FROM `pages` WHERE `id` = '".$id."'";
		
		if($result = $mysqli->query($query))
		{
			while ($urltmp = $result->fetch_assoc()) { $url = $urltmp; }
			$result->free();
		}
		
		return $url;
	}
	
	function getPage($mod, $profil=false)
	{
		$url = array();
		$db = Database::getInstance();
		$mysqli = $db->getConnection(); 
		$mysqli->set_charset( 'utf8');
		
		$mod = $mysqli->real_escape_string($mod); // Sécu
		$query = "SELECT * FROM `pages` WHERE `mod` = '".$mod."'";
		
		if($result = $mysqli->query($query))
		{
			while ($urltmp = $result->fetch_assoc()) { $url = $urltmp; }
			$result->free();
		}
		
		$url['ok'] = true;
		
		if($profil) 
		{
			if(!isset($url['id']))
			{
				$url['url'] = 'views/acces.php';
				$url['nom'] = 'Page inaccessible';
				$url['ok'] = false;
			}
			else
			{
				if(!isClear($profil, $url['id']))
				{
					$url['url'] = 'views/acces.php';
					$url['nom'] = 'Page inaccessible';
					$url['ok'] = false;
				}
			}
		}
		return $url;
	}
	
	function getProfils($onlynb = false)
	{
		$autorisations = array();
		$db = Database::getInstance();
		$mysqli = $db->getConnection(); 
		$mysqli->set_charset( 'utf8');
		$query = "SELECT * FROM `profils`";
		
		if($result = $mysqli->query($query)){
			while ($message = $result->fetch_assoc()) { $autorisations[] = $message; }
			$result->free();
		}

		if($onlynb)
			return count($autorisations);
		return $autorisations;
	}
	
	function getAutorisations($onlynb = false)
	{
		$autorisations = array();
		$db = Database::getInstance();
		$mysqli = $db->getConnection(); 
		$mysqli->set_charset( 'utf8');
		$query = "SELECT * FROM `autorisations`";
		
		if($result = $mysqli->query($query)){
			while ($message = $result->fetch_assoc()) { $autorisations[] = $message; }
			$result->free();
		}

		if($onlynb)
			return count($autorisations);
		return $autorisations;
	}
	
	function getAutorisation($page, $profil)
	{
		$autorisations = array();
		$db = Database::getInstance();
		$mysqli = $db->getConnection(); 
		$mysqli->set_charset( 'utf8');
		
		$page = (int)$page;
		$profil = (int)$profil;
		
		$query = "SELECT * FROM `autorisations` WHERE `page` = '".$page."' AND `profil` = '".$profil."'";
		
		if($result = $mysqli->query($query)){
			while ($message = $result->fetch_assoc()) { $autorisations[] = $message; }
			$result->free();
		}

		if(count($autorisations) && $autorisations[0]['etat'] == "1")
			return true;
		return false;
	}
	
	function changeAutorisation($page, $profil)
	{
		$autorisations = array();
		$db = Database::getInstance();
		$mysqli = $db->getConnection(); 
		$mysqli->set_charset( 'utf8');
		
		$page = (int)$page;
		$profil = (int)$profil;

		$query = "SELECT * FROM `autorisations` WHERE `page` = '".$page."' AND `profil` = '".$profil."'";
		
		if($result = $mysqli->query($query)){
			while ($message = $result->fetch_assoc()) { $autorisations[] = $message; }
			$result->free();
		}

		$actionLog = ""; // Pour savoir quoi logguer

		if(count($autorisations) && $autorisations[0]['etat'] == "1")
		{ 
			$query = "UPDATE `autorisations` SET
			 `etat` = '0'
			 WHERE `page` = '".$page."' AND `profil` = '".$profil."'";
		
			$mysqli->query($query);
			$actionLog = "Retrait accès";
		}
		elseif(count($autorisations) && $autorisations[0]['etat'] == "0")
		{
			$query = "UPDATE `autorisations` SET
			 `etat` = '1'
			 WHERE `page` = '".$page."' AND `profil` = '".$profil."'";
		
			$mysqli->query($query);
			$actionLog = "Ajout accès";
		}
		elseif(!count($autorisations))
		{
			$query = "INSERT INTO `autorisations` (`id`, `page`, `profil`,`etat`) VALUES (NULL, '".$page."', '".$profil."','1');";
			$mysqli->query($query);
			$actionLog = "Ajout accès (Initial)";
		}
		
		// LOG : Changement de droits
		if($actionLog != "") {
			$userId = $_SESSION['user']['id'] ?? 0;
			if(function_exists('logUserAction')) {
				logUserAction($userId, 'update_permission', "$actionLog pour Page ID $page / Profil ID $profil");
			}
		}
	}
?>