<?php 

	function insertCommande($panier)
	{	
		$json_panier = json_encode($panier['panier'],JSON_UNESCAPED_UNICODE);
		
		$totalttc = getCommandePrice($json_panier,$panier['id_promo']);
		
		$db = Database::getInstance();
		$mysqli = $db->getConnection(); 
		$query = "INSERT INTO `commandes` (`id`, `ref_commande`, `id_client`, `status`, `totalttc`, `id_promo`, `date_commande`, `id_transporteur`, `id_modepaiement`, `json_panier`) VALUES 
		(NULL, '".$panier['ref_commande']."', '".$panier['id_client']."', '".$panier['status']."', '".$totalttc."', '".$panier['id_promo']."', NOW(), '".$panier['id_transporteur']."', '".$panier['id_modepaiement']."', '".$json_panier."');";

		$mysqli->query($query);
		
		$commande_id = $mysqli->insert_id;
		
		return $commande_id;
	}
	
	function insetCardsFromCommande($id_commande)
	{
		$commande = getCommande($id_commande);
		
		$panier = json_decode($commande['json_panier']);
		
		foreach($panier as $element)
		{	
			$gradation = array();
			$gradation['id_commande'] = $id_commande;
			$gradation['ref_gradation'] = getReferenceUniqueCarte();
			
			if(isset($element->id) && $element->id == 'perso')
			{
				$lang = getLangFromSite($element->lang);
				
				$lang_id = $lang['id'];
				
				$name = $element->name;
				$num = $element->num;
				
				$set_name = $element->ext;
				$id = 0;
				$timecode = $element->timecode;
				$annee = '';
			}
			else
			{
				$carte = getCardByID($element->id);
				$id = $element->id;
				$set = getSet($carte['set_id']);
				
				$lang = getLangFromSiteForCardLangId($carte['lang_id']);
				$lang_id = $lang['id'];
				
				$name = $carte['name'];
				
				$num = $carte['local_id'];
				if(isset($set['cardcount_official']) && is_int((int)$carte['local_id']) && (int)$carte['local_id']!=0){ 
					$num .= '/'.$set['cardcount_official']; 
				}
				
				$set_name = $set['name'];

				$annee = '';
				if(isset($set['year']))
					$annee = substr($set['year'],0,4);
				$id = $carte['id'];
				$timecode = $element->timecode;
			}
			$gradation['id_carte'] = $id;
			$gradation['nom_carte'] = $name;
			$gradation['num_carte'] = $num;
			$gradation['nom_set'] = $set_name;
			$gradation['annee'] = $annee;
			$gradation['lang_id'] = $lang_id;
			if(isset($element->firsted) && $element->firsted == "true")
				$gradation['firsted'] = true;
			else
				$gradation['firsted'] = false;
			
			if(isset($element->promo) && $element->promo == "true")
				$gradation['promo'] = true;
			else
				$gradation['promo'] = false;
			
			if(isset($element->reverse) && $element->reverse == "true")
				$gradation['reverse'] = true;
			else
				$gradation['reverse'] = false;
			
			if(isset($element->holo) && $element->holo == "true")
				$gradation['holo'] = true;
			else
				$gradation['holo'] = false;
			
			if(isset($element->fullart) && $element->fullart == "true")
				$gradation['fullart'] = true;
			else
				$gradation['fullart'] = false;
			
			if(isset($element->metal) && $element->metal == "true")
				$gradation['metal'] = true;
			else
				$gradation['metal'] = false;
			
			if(isset($element->gradation) && $element->gradation == "1")
			{
				$gradation['dfu'] = false;
				$gradation['authentification'] = true;
				$gradation['notation'] = true;
			}
			elseif(isset($element->gradation) && $element->gradation == "2")
			{
				$gradation['dfu'] = false;
				$gradation['authentification'] = true;
				$gradation['notation'] = false;
			}
			elseif(isset($element->gradation) && $element->gradation == "3")
			{
				$gradation['dfu'] = true;
				$gradation['authentification'] = false;
				$gradation['notation'] = false;
			}
			else
			{
				$gradation['dfu'] = false;
				$gradation['authentification'] = true;
				$gradation['notation'] = true;
			}
			
			$gradation['valeurdeclaree'] = $element->value;
			
			if(isset($element->cbx) && $element->cbx == "true")
				$gradation['lang_cert'] = true;
			else
				$gradation['lang_cert'] = false;
			
			//var_dump($gradation);
			
			insertGradation($gradation);
		}
	}
	
	function getTarifTransport($id_transporteur,$valeur_declaree)
	{
		$sets = array();
		$db = Database::getInstance();
		$mysqli = $db->getConnection(); 
		$mysqli->set_charset( 'utf8');
		$query = "SELECT * FROM `tarifs_transport` WHERE '".$valeur_declaree."' BETWEEN `prix_mini` AND `prix_maxi` AND `id_transporteur` = '".$id_transporteur."'";
		
		if($result = $mysqli->query($query)){
			while ($message = $result->fetch_assoc()) { $sets = $message; }
			$result->free();
		}
		
		return $sets['tarif'];
	}
	
	function getCommandePrice($json_panier,$id_promo=0)
	{
		$panier = json_decode($json_panier);
		
		$totalttc = 0;
		$percent = 0;
		$totaldeclare = 0;
		
		if(isset($id_promo) && $id_promo != 0)
		{
			$promo = getPromoById($id_promo);
			$percent = $promo['percent'];
		}
		
		foreach($panier as $element)
		{			
			if(isset($element->value)) {
				$pu = getConfigurationByPrice($element->value);
				$totaldeclare = $totaldeclare + $element->value;
			}
			else
				$pu = getConfigurationByPrice(0);
				
			$totalttc = $totalttc + (int)$pu['price'];
		}
		
		$totalttc = ($totalttc * (100 - $percent))/100;
		
		$fdp = getTarifTransport(1,$totaldeclare);
		
		$totalttc = $totalttc + $fdp;
		
		return $totalttc;
	}
	
	function editCommande($commande)
	{
		$db = Database::getInstance();
		$mysqli = $db->getConnection(); 
		$query = "UPDATE `commandes` SET
		 `email` = '".$commande['email']."'";
		 
		$query .= "WHERE `id` = '".$commande['id']."';";
		
		return $mysqli->query($query);
	}
	
	function getCommandes()
	{
		$commande = array();
		$db = Database::getInstance();
		$mysqli = $db->getConnection(); 
		$mysqli->set_charset( 'utf8');
		$query = "SELECT * FROM `commandes` ORDER by id DESC;";
		
		if($result = $mysqli->query($query)){
			while ($message = $result->fetch_assoc()) { $commande[] = $message; }
			$result->free();
		}
		
		return $commande;
	}
	
	function getCommande($id)
	{
		$commande = array();
		$db = Database::getInstance();
		$mysqli = $db->getConnection(); 
		$mysqli->set_charset( 'utf8');
		$query = "SELECT * FROM `commandes` WHERE `id` = '".$id."';";
		
		if($result = $mysqli->query($query)){
			while ($message = $result->fetch_assoc()) { $commande[] = $message; }
			$result->free();
		}
		
		return $commande[0];
	}
	
	function getTracking($id)
	{
		$commande = array();
		$db = Database::getInstance();
		$mysqli = $db->getConnection(); 
		$mysqli->set_charset( 'utf8');
		$query = "SELECT * FROM `tracking` WHERE `id_commande` = '".$id."' AND `customer` = '0';";
		
		if($result = $mysqli->query($query)){
			while ($message = $result->fetch_assoc()) { $commande = $message; }
			$result->free();
		}
		
		if(isset($commande['tracking']))
			return $commande['tracking'];
		return false;
	}
	
	function getTrackingClient($id)
	{
		$commande = array();
		$db = Database::getInstance();
		$mysqli = $db->getConnection(); 
		$mysqli->set_charset( 'utf8');
		$query = "SELECT * FROM `tracking` WHERE `id_commande` = '".$id."' AND `customer` = '1';";
		
		if($result = $mysqli->query($query)){
			while ($message = $result->fetch_assoc()) { $commande = $message; }
			$result->free();
		}
		
		if(isset($commande['tracking']))
			return $commande['tracking'];
		return false;
	}
	
	function getCommandesClient($id_client)
	{
		$commande = array();
		$db = Database::getInstance();
		$mysqli = $db->getConnection(); 
		$mysqli->set_charset( 'utf8');
		$query = "SELECT * FROM `commandes` WHERE `id_client` = '".$id_client."';";
		
		if($result = $mysqli->query($query)){
			while ($message = $result->fetch_assoc()) { $commande[] = $message; }
			$result->free();
		}
		
		return $commande;
	}
	
	function setTrackingClient($id_commande,$tracking)
	{
		$db = Database::getInstance();
		$mysqli = $db->getConnection(); 
		
		$query = '';
		
		if($tracking == '')
			$tracking = 'Aucun';
		
		if(getTrackingClient($id_commande))
		{
			$query .= "UPDATE `tracking` SET
			 `tracking` = '".$tracking."'";
			$query .= " WHERE `id_commande` = '".$id_commande."' AND customer = 1;";
		}
		else
		{
			$query .= "INSERT INTO `tracking`  (`id`, `id_commande`, `tracking`, `customer`) 
			VALUES (NULL, '".$id_commande."', '".$tracking."', '1');";
		}
		
		return $mysqli->query($query);
	}
	
	function setTracking($id_commande,$tracking)
	{
		$db = Database::getInstance();
		$mysqli = $db->getConnection(); 
		
		$query = '';
		
		if($tracking == '')
			$tracking = 'Aucun';
		
		if(getTracking($id_commande))
		{
			$query .= "UPDATE `tracking` SET
			 `tracking` = '".$tracking."'";
			$query .= " WHERE `id_commande` = '".$id_commande."' AND customer = 0;";
		}
		else
		{
			$query .= "INSERT INTO `tracking`  (`id`, `id_commande`, `tracking`, `customer`) 
			VALUES (NULL, '".$id_commande."', '".$tracking."', '0');";
		}
		
		return $mysqli->query($query);
	}
	
	function getStatus($id)
	{
		$commande = array();
		$db = Database::getInstance();
		$mysqli = $db->getConnection(); 
		$mysqli->set_charset( 'utf8');
		$query = "SELECT * FROM `status` WHERE `id` = '".$id."';";
		
		if($result = $mysqli->query($query)){
			while ($message = $result->fetch_assoc()) { $commande[] = $message; }
			$result->free();
		}
		
		return $commande[0];
	}
	
	function getAllStatus()
	{
		$commande = array();
		$db = Database::getInstance();
		$mysqli = $db->getConnection(); 
		$mysqli->set_charset( 'utf8');
		$query = "SELECT * FROM `status`;";
		
		if($result = $mysqli->query($query)){
			while ($message = $result->fetch_assoc()) { $commande[] = $message; }
			$result->free();
		}
		
		return $commande;
	}
	
	function setStatus($id_commande,$status)
	{
		$db = Database::getInstance();
		$mysqli = $db->getConnection(); 
		$mysqli->set_charset( 'utf8');
		$query = "UPDATE `commandes` SET
			 `status` = '".$status."'";
		$query .= " WHERE `id` = '".$id_commande."'";
		
		return $mysqli->query($query);
	}

?>