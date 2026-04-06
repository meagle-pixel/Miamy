<?php

	/* ==========================================================================
	   GESTION DES PAGES & DROITS (ACL)
	   ========================================================================== */

	function insertPage($nom, $mod, $url)
	{
		$pdo  = Database::getInstance()->getConnection();
		$stmt = $pdo->prepare(
			"INSERT INTO `pages` (`id`, `nom`, `mod`, `url`) VALUES (NULL, ?, ?, ?)"
		);
		$stmt->execute([$nom, $mod, $url]);
		$idp = $pdo->lastInsertId();

		if ($idp && function_exists('logUserAction')) {
			$userId = $_SESSION['user']['id'] ?? 0;
			logUserAction($userId, 'create_page', "Création de la page : $nom (Module: $mod)");
		}

		return $idp ?: false;
	}

	function updatePage($id, $nom, $mod, $url)
	{
		$pdo  = Database::getInstance()->getConnection();
		$stmt = $pdo->prepare(
			"UPDATE `pages` SET `nom` = ?, `mod` = ?, `url` = ? WHERE `id` = ?"
		);
		$res = $stmt->execute([$nom, $mod, $url, (int)$id]);

		if ($res && function_exists('logUserAction')) {
			$userId = $_SESSION['user']['id'] ?? 0;
			logUserAction($userId, 'update_page', "Modification de la page ID $id : $nom");
		}

		return true;
	}

	function getPages($onlynb = false)
	{
		$pdo  = Database::getInstance()->getConnection();
		$data = $pdo->query("SELECT * FROM `pages` ORDER BY `nom` ASC")->fetchAll();
		return $onlynb ? count($data) : $data;
	}

	function getPageById($id)
	{
		$pdo  = Database::getInstance()->getConnection();
		$stmt = $pdo->prepare("SELECT * FROM `pages` WHERE `id` = ?");
		$stmt->execute([(int)$id]);
		return $stmt->fetch() ?: [];
	}

	function getPage($mod, $profil = false)
	{
		$pdo  = Database::getInstance()->getConnection();
		$stmt = $pdo->prepare("SELECT * FROM `pages` WHERE `mod` = ?");
		$stmt->execute([$mod]);
		$url = $stmt->fetch() ?: [];

		$url['ok'] = true;

		if ($profil) {
			if (!isset($url['id'])) {
				$url['url'] = 'views/acces.php';
				$url['nom'] = 'Page inaccessible';
				$url['ok']  = false;
			} else {
				if (!isClear($profil, $url['id'])) {
					$url['url'] = 'views/acces.php';
					$url['nom'] = 'Page inaccessible';
					$url['ok']  = false;
				}
			}
		}
		return $url;
	}

	function getProfils($onlynb = false)
	{
		$pdo  = Database::getInstance()->getConnection();
		$data = $pdo->query("SELECT * FROM `profils`")->fetchAll();
		return $onlynb ? count($data) : $data;
	}

	function getAutorisations($onlynb = false)
	{
		$pdo  = Database::getInstance()->getConnection();
		$data = $pdo->query("SELECT * FROM `autorisations`")->fetchAll();
		return $onlynb ? count($data) : $data;
	}

	function getAutorisation($page, $profil)
	{
		$pdo  = Database::getInstance()->getConnection();
		$stmt = $pdo->prepare("SELECT * FROM `autorisations` WHERE `page` = ? AND `profil` = ?");
		$stmt->execute([(int)$page, (int)$profil]);
		$rows = $stmt->fetchAll();

		return count($rows) && $rows[0]['etat'] == "1";
	}

	function changeAutorisation($page, $profil)
	{
		$pdo  = Database::getInstance()->getConnection();
		$stmt = $pdo->prepare("SELECT * FROM `autorisations` WHERE `page` = ? AND `profil` = ?");
		$stmt->execute([(int)$page, (int)$profil]);
		$rows      = $stmt->fetchAll();
		$actionLog = "";

		if (count($rows) && $rows[0]['etat'] == "1") {
			$upd = $pdo->prepare("UPDATE `autorisations` SET `etat` = '0' WHERE `page` = ? AND `profil` = ?");
			$upd->execute([(int)$page, (int)$profil]);
			$actionLog = "Retrait accès";
		} elseif (count($rows) && $rows[0]['etat'] == "0") {
			$upd = $pdo->prepare("UPDATE `autorisations` SET `etat` = '1' WHERE `page` = ? AND `profil` = ?");
			$upd->execute([(int)$page, (int)$profil]);
			$actionLog = "Ajout accès";
		} else {
			$ins = $pdo->prepare("INSERT INTO `autorisations` (`id`, `page`, `profil`, `etat`) VALUES (NULL, ?, ?, '1')");
			$ins->execute([(int)$page, (int)$profil]);
			$actionLog = "Ajout accès (Initial)";
		}

		if ($actionLog !== "" && function_exists('logUserAction')) {
			$userId = $_SESSION['user']['id'] ?? 0;
			logUserAction($userId, 'update_permission', "$actionLog pour Page ID $page / Profil ID $profil");
		}
	}
?>
