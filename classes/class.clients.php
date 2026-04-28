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
		$stmt = $pdo->prepare("DELETE FROM `clients` WHERE `id` = :id");
		return $stmt->execute(['id' => (int)$id]);
	}

	function getClient($id)
	{
		$pdo  = Database::getInstance()->getConnection();
		$stmt = $pdo->prepare("SELECT * FROM `clients` WHERE `id` = :id");
		$stmt->execute(['id' => (int)$id]);
		return $stmt->fetch() ?: [];
	}

	function getClientByEmail($email)
	{
		$pdo  = Database::getInstance()->getConnection();
		$stmt = $pdo->prepare("SELECT * FROM `clients` WHERE `email` = :email");
		$stmt->execute(['email' => $email]);
		return $stmt->fetch() ?: [];
	}

	function insertClient($client)
	{
		$pdo  = Database::getInstance()->getConnection();
		$stmt = $pdo->prepare(
			"INSERT INTO `clients`
			(`id`, `civilite`, `nom`, `prenom`, `telephone`,
			 `adresse`, `adresse_comp`, `codepostal`, `ville`)
			VALUES
			(NULL, :civilite, :nom, :prenom, :telephone,
			 :adresse, :adresse_comp, :codepostal, :ville)"
		);
		$stmt->execute([
			'civilite'     => $client['civilite'],
			'nom'          => $client['nom'],
			'prenom'       => $client['prenom'],
			'telephone'    => $client['telephone'],
			'adresse'      => $client['adresse'],
			'adresse_comp' => $client['adresse_comp'],
			'codepostal'   => $client['codepostal'],
			'ville'        => $client['ville'],
		]);
		return $pdo->lastInsertId();
	}

	function updateClientDataLite($id)
	{
		$pdo  = Database::getInstance()->getConnection();
		$stmt = $pdo->prepare("SELECT * FROM `clients` WHERE `id` = :id");
		$stmt->execute(['id' => (int)$id]);
		$result = $stmt->fetch();

		if ($result) {
			$_SESSION['connected'] = true;
			$_SESSION['user']      = $result;
		} else {
			$_SESSION['connected'] = false;
			$_SESSION['user']      = false;
		}

		$upd = $pdo->prepare("UPDATE `clients` SET `dateaction` = NOW() WHERE `id` = :id");
		$upd->execute(['id' => (int)$id]);

		insertIP($id, 2);
	}

	function trytoconnectClient($email, $pass)
	{
		$pdo  = Database::getInstance()->getConnection();
		$stmt = $pdo->prepare("SELECT * FROM `clients` WHERE `email` = :email");
		$stmt->execute(['email' => $email]);
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
			`email` = :email, `civilite` = :civilite, `nom` = :nom, `prenom` = :prenom,
			`telephone` = :telephone, `adresse` = :adresse, `adresse_comp` = :adresse_comp,
			`codepostal` = :codepostal, `ville` = :ville
			WHERE `id` = :id"
		);
		$stmt->execute([
			'email'        => $client['email'],
			'civilite'     => $client['civilite'],
			'nom'          => $client['nom'],
			'prenom'       => $client['prenom'],
			'telephone'    => $client['telephone'],
			'adresse'      => $client['adresse'],
			'adresse_comp' => $client['adresse_comp'],
			'codepostal'   => $client['codepostal'],
			'ville'        => $client['ville'],
			'id'           => (int)$client['id'],
		]);

		if (isset($client['motdepasse'])) {
			$upd = $pdo->prepare("UPDATE `clients` SET `motdepasse` = :motdepasse WHERE `id` = :id");
			$upd->execute([
				'motdepasse' => password_hash($client['motdepasse'], PASSWORD_DEFAULT),
				'id'         => (int)$client['id'],
			]);
		}
	}

	function changePassword($id, $pass)
	{
		$pdo  = Database::getInstance()->getConnection();
		$stmt = $pdo->prepare("UPDATE `clients` SET `motdepasse` = :motdepasse WHERE `id` = :id");
		$stmt->execute([
			'motdepasse' => password_hash($pass, PASSWORD_DEFAULT),
			'id'         => (int)$id,
		]);
	}

	function existEmailClient($email)
	{
		$pdo  = Database::getInstance()->getConnection();
		$stmt = $pdo->prepare("SELECT id FROM `clients` WHERE `email` = :email");
		$stmt->execute(['email' => $email]);
		return $stmt->fetch() !== false;
	}

?>
