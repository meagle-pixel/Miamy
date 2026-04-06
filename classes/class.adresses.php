<?php

	function getAdress($id)
	{
		$pdo  = Database::getInstance()->getConnection();
		$stmt = $pdo->prepare("SELECT * FROM `adresses` WHERE id = ?");
		$stmt->execute([(int)$id]);
		return $stmt->fetch() ?: [];
	}

	function insertAdress($adresse)
	{
		$pdo  = Database::getInstance()->getConnection();
		$stmt = $pdo->prepare(
			"INSERT INTO `adresses` (`id`, `libelle`, `adresse`, `adresse_comp`, `codepostal`, `ville`)
			 VALUES (NULL, ?, ?, ?, ?, ?)"
		);
		$stmt->execute([
			$adresse['libelle'],
			$adresse['adresse'],
			$adresse['adresse_comp'],
			$adresse['codepostal'],
			$adresse['ville'],
		]);
		return $pdo->lastInsertId();
	}

	function updateAdress($adresse)
	{
		$pdo  = Database::getInstance()->getConnection();
		$stmt = $pdo->prepare(
			"UPDATE `adresses` SET
			`libelle` = ?, `adresse` = ?, `adresse_comp` = ?, `codepostal` = ?, `ville` = ?
			WHERE `id` = ?"
		);
		$stmt->execute([
			$adresse['libelle'],
			$adresse['adresse'],
			$adresse['adresse_comp'],
			$adresse['codepostal'],
			$adresse['ville'],
			(int)$adresse['id'],
		]);
	}

	function deleteAdresses($ids)
	{
		$adresses = explode(',', $ids);
		$pdo      = Database::getInstance()->getConnection();

		$stmt = $pdo->prepare("DELETE FROM `adresses` WHERE `id` = ?");
		$stmt->execute([(int)$adresses[0]]);

		if (isset($adresses[1])) {
			$stmt->execute([(int)$adresses[1]]);
		}
	}
?>
