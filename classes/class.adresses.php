<?php

	function getAdress($id)
	{
		$pdo  = Database::getInstance()->getConnection();
		$stmt = $pdo->prepare("SELECT * FROM `adresses` WHERE id = :id");
		$stmt->execute(['id' => (int)$id]);
		return $stmt->fetch() ?: [];
	}

	function insertAdress($adresse)
	{
		$pdo  = Database::getInstance()->getConnection();
		$stmt = $pdo->prepare(
			"INSERT INTO `adresses` (`id`, `libelle`, `adresse`, `adresse_comp`, `codepostal`, `ville`)
			 VALUES (NULL, :libelle, :adresse, :adresse_comp, :codepostal, :ville)"
		);
		$stmt->execute([
			'libelle'      => $adresse['libelle'],
			'adresse'      => $adresse['adresse'],
			'adresse_comp' => $adresse['adresse_comp'],
			'codepostal'   => $adresse['codepostal'],
			'ville'        => $adresse['ville'],
		]);
		return $pdo->lastInsertId();
	}

	function updateAdress($adresse)
	{
		$pdo  = Database::getInstance()->getConnection();
		$stmt = $pdo->prepare(
			"UPDATE `adresses` SET
			`libelle` = :libelle, `adresse` = :adresse, `adresse_comp` = :adresse_comp,
			`codepostal` = :codepostal, `ville` = :ville
			WHERE `id` = :id"
		);
		$stmt->execute([
			'libelle'      => $adresse['libelle'],
			'adresse'      => $adresse['adresse'],
			'adresse_comp' => $adresse['adresse_comp'],
			'codepostal'   => $adresse['codepostal'],
			'ville'        => $adresse['ville'],
			'id'           => (int)$adresse['id'],
		]);
	}

	function deleteAdresses($ids)
	{
		$adresses = explode(',', $ids);
		$pdo      = Database::getInstance()->getConnection();

		$stmt = $pdo->prepare("DELETE FROM `adresses` WHERE `id` = :id");
		$stmt->execute(['id' => (int)$adresses[0]]);

		if (isset($adresses[1])) {
			$stmt->execute(['id' => (int)$adresses[1]]);
		}
	}
?>
