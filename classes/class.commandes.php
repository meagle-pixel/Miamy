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
			VALUES (NULL, :ref_commande, :id_client, :status, :totalttc, :id_promo,
			        NOW(), :id_transporteur, :id_modepaiement, :json_panier)"
		);
		$stmt->execute([
			'ref_commande'    => $panier['ref_commande'],
			'id_client'       => $panier['id_client'],
			'status'          => $panier['status'],
			'totalttc'        => $totalttc,
			'id_promo'        => $panier['id_promo'],
			'id_transporteur' => $panier['id_transporteur'],
			'id_modepaiement' => $panier['id_modepaiement'],
			'json_panier'     => $json_panier,
		]);
		return $pdo->lastInsertId();
	}

	function getTarifTransport($id_transporteur, $valeur_declaree)
	{
		$pdo  = Database::getInstance()->getConnection();
		$stmt = $pdo->prepare(
			"SELECT * FROM `tarifs_transport`
			 WHERE :valeur_declaree BETWEEN `prix_mini` AND `prix_maxi`
			 AND `id_transporteur` = :id_transporteur"
		);
		$stmt->execute([
			'valeur_declaree' => $valeur_declaree,
			'id_transporteur' => $id_transporteur,
		]);
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
		$stmt = $pdo->prepare("UPDATE `commandes` SET `email` = :email WHERE `id` = :id");
		return $stmt->execute([
			'email' => $commande['email'],
			'id'    => (int)$commande['id'],
		]);
	}

	function getCommandes()
	{
		$pdo = Database::getInstance()->getConnection();
		return $pdo->query("SELECT * FROM `commandes` ORDER BY id DESC")->fetchAll();
	}

	function getCommande($id)
	{
		$pdo  = Database::getInstance()->getConnection();
		$stmt = $pdo->prepare("SELECT * FROM `commandes` WHERE `id` = :id");
		$stmt->execute(['id' => (int)$id]);
		return $stmt->fetch() ?: [];
	}

	function getTracking($id)
	{
		$pdo  = Database::getInstance()->getConnection();
		$stmt = $pdo->prepare("SELECT * FROM `tracking` WHERE `id_commande` = :id_commande AND `customer` = '0'");
		$stmt->execute(['id_commande' => (int)$id]);
		$row = $stmt->fetch();
		return $row['tracking'] ?? false;
	}

	function getTrackingClient($id)
	{
		$pdo  = Database::getInstance()->getConnection();
		$stmt = $pdo->prepare("SELECT * FROM `tracking` WHERE `id_commande` = :id_commande AND `customer` = '1'");
		$stmt->execute(['id_commande' => (int)$id]);
		$row = $stmt->fetch();
		return $row['tracking'] ?? false;
	}

	function getCommandesClient($id_client)
	{
		$pdo  = Database::getInstance()->getConnection();
		$stmt = $pdo->prepare("SELECT * FROM `commandes` WHERE `id_client` = :id_client");
		$stmt->execute(['id_client' => (int)$id_client]);
		return $stmt->fetchAll();
	}

	function setTrackingClient($id_commande, $tracking)
	{
		$pdo      = Database::getInstance()->getConnection();
		$tracking = ($tracking === '') ? 'Aucun' : $tracking;

		if (getTrackingClient($id_commande)) {
			$stmt = $pdo->prepare("UPDATE `tracking` SET `tracking` = :tracking WHERE `id_commande` = :id_commande AND customer = 1");
			return $stmt->execute([
				'tracking'    => $tracking,
				'id_commande' => (int)$id_commande,
			]);
		} else {
			$stmt = $pdo->prepare("INSERT INTO `tracking` (`id`, `id_commande`, `tracking`, `customer`) VALUES (NULL, :id_commande, :tracking, '1')");
			return $stmt->execute([
				'id_commande' => (int)$id_commande,
				'tracking'    => $tracking,
			]);
		}
	}

	function setTracking($id_commande, $tracking)
	{
		$pdo      = Database::getInstance()->getConnection();
		$tracking = ($tracking === '') ? 'Aucun' : $tracking;

		if (getTracking($id_commande)) {
			$stmt = $pdo->prepare("UPDATE `tracking` SET `tracking` = :tracking WHERE `id_commande` = :id_commande AND customer = 0");
			return $stmt->execute([
				'tracking'    => $tracking,
				'id_commande' => (int)$id_commande,
			]);
		} else {
			$stmt = $pdo->prepare("INSERT INTO `tracking` (`id`, `id_commande`, `tracking`, `customer`) VALUES (NULL, :id_commande, :tracking, '0')");
			return $stmt->execute([
				'id_commande' => (int)$id_commande,
				'tracking'    => $tracking,
			]);
		}
	}

	function getStatus($id)
	{
		$pdo  = Database::getInstance()->getConnection();
		$stmt = $pdo->prepare("SELECT * FROM `status` WHERE `id` = :id");
		$stmt->execute(['id' => (int)$id]);
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
		$stmt = $pdo->prepare("UPDATE `commandes` SET `status` = :status WHERE `id` = :id");
		return $stmt->execute([
			'status' => $status,
			'id'     => (int)$id_commande,
		]);
	}

?>
