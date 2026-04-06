<?php

	function getAllClients($onlynb = false)
	{
		$pdo  = Database::getInstance()->getConnection();
		$data = $pdo->query("SELECT * FROM `clients`")->fetchAll();
		return $onlynb ? count($data) : $data;
	}

	function deleteClient($id)
	{
		$pdo  = Database::getInstance()->getConnection();
		$stmt = $pdo->prepare("DELETE FROM `clients` WHERE `id` = ?");
		return $stmt->execute([(int)$id]);
	}

	function getClient($id)
	{
		$pdo  = Database::getInstance()->getConnection();
		$stmt = $pdo->prepare("SELECT * FROM `clients` WHERE `id` = ?");
		$stmt->execute([(int)$id]);
		return $stmt->fetch() ?: [];
	}

	function getClientByEmail($email)
	{
		$pdo  = Database::getInstance()->getConnection();
		$stmt = $pdo->prepare("SELECT * FROM `clients` WHERE `email` = ?");
		$stmt->execute([$email]);
		return $stmt->fetch() ?: [];
	}

	function insertClient($client)
	{
		$pdo  = Database::getInstance()->getConnection();
		$stmt = $pdo->prepare(
			"INSERT INTO `clients`
			(`id`, `email`, `motdepasse`, `civilite`, `nom`, `prenom`, `telephone`,
			 `adresse`, `adresse_comp`, `codepostal`, `ville`,
			 `dateinscription`, `dateconnect`, `dateaction`)
			VALUES
			(NULL, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW(), NOW())"
		);
		$stmt->execute([
			$client['email'],
			password_hash($client['motdepasse'], PASSWORD_DEFAULT),
			$client['civilite'],
			$client['nom'],
			$client['prenom'],
			$client['telephone'],
			$client['adresse'],
			$client['adresse_comp'],
			$client['codepostal'],
			$client['ville'],
		]);
		return $pdo->lastInsertId();
	}

	function updateClientDataLite($id)
	{
		$pdo  = Database::getInstance()->getConnection();
		$stmt = $pdo->prepare("SELECT * FROM `clients` WHERE `id` = ?");
		$stmt->execute([(int)$id]);
		$result = $stmt->fetch();

		if ($result) {
			$_SESSION['connected'] = true;
			$_SESSION['user']      = $result;
		} else {
			$_SESSION['connected'] = false;
			$_SESSION['user']      = false;
		}

		$upd = $pdo->prepare("UPDATE `clients` SET `dateaction` = NOW() WHERE `id` = ?");
		$upd->execute([(int)$id]);

		insertIP($id, 2);
	}

	function trytoconnectClient($email, $pass)
	{
		$pdo  = Database::getInstance()->getConnection();
		$stmt = $pdo->prepare("SELECT * FROM `clients` WHERE `email` = ?");
		$stmt->execute([$email]);
		$user = $stmt->fetch();

		if ($user && password_verify($pass, $user['motdepasse'])) {
			$_SESSION['connected'] = true;
			$_SESSION['user']      = $user;
			return true;
		}

		$_SESSION['connected'] = false;
		$_SESSION['user']      = false;
		return false;
	}

	function updateClient($client)
	{
		$pdo  = Database::getInstance()->getConnection();
		$stmt = $pdo->prepare(
			"UPDATE `clients` SET
			`email` = ?, `civilite` = ?, `nom` = ?, `prenom` = ?,
			`telephone` = ?, `adresse` = ?, `adresse_comp` = ?,
			`codepostal` = ?, `ville` = ?
			WHERE `id` = ?"
		);
		$stmt->execute([
			$client['email'],
			$client['civilite'],
			$client['nom'],
			$client['prenom'],
			$client['telephone'],
			$client['adresse'],
			$client['adresse_comp'],
			$client['codepostal'],
			$client['ville'],
			(int)$client['id'],
		]);

		if (isset($client['motdepasse'])) {
			$upd = $pdo->prepare("UPDATE `clients` SET `motdepasse` = ? WHERE `id` = ?");
			$upd->execute([password_hash($client['motdepasse'], PASSWORD_DEFAULT), (int)$client['id']]);
		}
	}

	function changePassword($id, $pass)
	{
		$pdo  = Database::getInstance()->getConnection();
		$stmt = $pdo->prepare("UPDATE `clients` SET `motdepasse` = ? WHERE `id` = ?");
		$stmt->execute([password_hash($pass, PASSWORD_DEFAULT), (int)$id]);
	}

	function existEmailClient($email)
	{
		$pdo  = Database::getInstance()->getConnection();
		$stmt = $pdo->prepare("SELECT id FROM `clients` WHERE `email` = ?");
		$stmt->execute([$email]);
		return $stmt->fetch() !== false;
	}

?>
