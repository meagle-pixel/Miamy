<?php

	function insertCommande($panier)
	{
		$json_panier = json_encode($panier['panier'], JSON_UNESCAPED_UNICODE);
		$totalttc    = getCommandePrice($json_panier, $panier['id_promo']);

		$pdo  = Database::getInstance()->getConnection();
		$stmt = $pdo->prepare(
			"INSERT INTO `commandes`
			(`id`, `ref_commande`, `id_client`, `status`, `totalttc`, `id_promo`,
			 `date_commande`, `id_transporteur`, `id_modepaiement`, `json_panier`)
			VALUES (NULL, ?, ?, ?, ?, ?, NOW(), ?, ?, ?)"
		);
		$stmt->execute([
			$panier['ref_commande'],
			$panier['id_client'],
			$panier['status'],
			$totalttc,
			$panier['id_promo'],
			$panier['id_transporteur'],
			$panier['id_modepaiement'],
			$json_panier,
		]);
		return $pdo->lastInsertId();
	}

	function getTarifTransport($id_transporteur, $valeur_declaree)
	{
		$pdo  = Database::getInstance()->getConnection();
		$stmt = $pdo->prepare(
			"SELECT * FROM `tarifs_transport`
			 WHERE ? BETWEEN `prix_mini` AND `prix_maxi`
			 AND `id_transporteur` = ?"
		);
		$stmt->execute([$valeur_declaree, $id_transporteur]);
		$row = $stmt->fetch();
		return $row['tarif'] ?? null;
	}

	function getCommandePrice($json_panier, $id_promo = 0)
	{
		$panier       = json_decode($json_panier);
		$totalttc     = 0;
		$percent      = 0;
		$totaldeclare = 0;

		if (isset($id_promo) && $id_promo != 0) {
			$promo   = getPromoById($id_promo);
			$percent = $promo['percent'];
		}

		foreach ($panier as $element) {
			$pu = isset($element->value)
				? getConfigurationByPrice($element->value)
				: getConfigurationByPrice(0);

			if (isset($element->value)) $totaldeclare += $element->value;
			$totalttc += (int)$pu['price'];
		}

		$totalttc = ($totalttc * (100 - $percent)) / 100;
		$fdp      = getTarifTransport(1, $totaldeclare);
		$totalttc = $totalttc + $fdp;

		return $totalttc;
	}

	function editCommande($commande)
	{
		$pdo  = Database::getInstance()->getConnection();
		$stmt = $pdo->prepare("UPDATE `commandes` SET `email` = ? WHERE `id` = ?");
		return $stmt->execute([$commande['email'], (int)$commande['id']]);
	}

	function getCommandes()
	{
		$pdo = Database::getInstance()->getConnection();
		return $pdo->query("SELECT * FROM `commandes` ORDER BY id DESC")->fetchAll();
	}

	function getCommande($id)
	{
		$pdo  = Database::getInstance()->getConnection();
		$stmt = $pdo->prepare("SELECT * FROM `commandes` WHERE `id` = ?");
		$stmt->execute([(int)$id]);
		return $stmt->fetch() ?: [];
	}

	function getTracking($id)
	{
		$pdo  = Database::getInstance()->getConnection();
		$stmt = $pdo->prepare("SELECT * FROM `tracking` WHERE `id_commande` = ? AND `customer` = '0'");
		$stmt->execute([(int)$id]);
		$row = $stmt->fetch();
		return $row['tracking'] ?? false;
	}

	function getTrackingClient($id)
	{
		$pdo  = Database::getInstance()->getConnection();
		$stmt = $pdo->prepare("SELECT * FROM `tracking` WHERE `id_commande` = ? AND `customer` = '1'");
		$stmt->execute([(int)$id]);
		$row = $stmt->fetch();
		return $row['tracking'] ?? false;
	}

	function getCommandesClient($id_client)
	{
		$pdo  = Database::getInstance()->getConnection();
		$stmt = $pdo->prepare("SELECT * FROM `commandes` WHERE `id_client` = ?");
		$stmt->execute([(int)$id_client]);
		return $stmt->fetchAll();
	}

	function setTrackingClient($id_commande, $tracking)
	{
		$pdo     = Database::getInstance()->getConnection();
		$tracking = ($tracking === '') ? 'Aucun' : $tracking;

		if (getTrackingClient($id_commande)) {
			$stmt = $pdo->prepare("UPDATE `tracking` SET `tracking` = ? WHERE `id_commande` = ? AND customer = 1");
			return $stmt->execute([$tracking, (int)$id_commande]);
		} else {
			$stmt = $pdo->prepare("INSERT INTO `tracking` (`id`, `id_commande`, `tracking`, `customer`) VALUES (NULL, ?, ?, '1')");
			return $stmt->execute([(int)$id_commande, $tracking]);
		}
	}

	function setTracking($id_commande, $tracking)
	{
		$pdo     = Database::getInstance()->getConnection();
		$tracking = ($tracking === '') ? 'Aucun' : $tracking;

		if (getTracking($id_commande)) {
			$stmt = $pdo->prepare("UPDATE `tracking` SET `tracking` = ? WHERE `id_commande` = ? AND customer = 0");
			return $stmt->execute([$tracking, (int)$id_commande]);
		} else {
			$stmt = $pdo->prepare("INSERT INTO `tracking` (`id`, `id_commande`, `tracking`, `customer`) VALUES (NULL, ?, ?, '0')");
			return $stmt->execute([(int)$id_commande, $tracking]);
		}
	}

	function getStatus($id)
	{
		$pdo  = Database::getInstance()->getConnection();
		$stmt = $pdo->prepare("SELECT * FROM `status` WHERE `id` = ?");
		$stmt->execute([(int)$id]);
		return $stmt->fetch() ?: [];
	}

	function getAllStatus()
	{
		$pdo = Database::getInstance()->getConnection();
		return $pdo->query("SELECT * FROM `status`")->fetchAll();
	}

	function setStatus($id_commande, $status)
	{
		$pdo  = Database::getInstance()->getConnection();
		$stmt = $pdo->prepare("UPDATE `commandes` SET `status` = ? WHERE `id` = ?");
		return $stmt->execute([$status, (int)$id_commande]);
	}

?>
