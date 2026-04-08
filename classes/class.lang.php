<?php

	function getAllLangsFromSite()
	{
		$pdo = Database::getInstance()->getConnection();
		return $pdo->query("SELECT * FROM `langues`")->fetchAll();
	}

	function getAllLangs()
	{
		$pdo = Database::getInstance()->getConnection();
		return $pdo->query("SELECT * FROM `pokemon-languages`")->fetchAll();
	}

	function getLang($id)
	{
		$pdo  = Database::getInstance()->getConnection();
		$stmt = $pdo->prepare("SELECT * FROM `pokemon-languages` WHERE `id` = :id");
		$stmt->execute(['id' => (int)$id]);
		return $stmt->fetch() ?: [];
	}

	function getLangFromSite($id)
	{
		$pdo  = Database::getInstance()->getConnection();
		$stmt = $pdo->prepare("SELECT * FROM `langues` WHERE `id` = :id");
		$stmt->execute(['id' => (int)$id]);
		return $stmt->fetch() ?: [];
	}

	function getLangFromSiteForCardLangId($id)
	{
		$pdo  = Database::getInstance()->getConnection();
		$stmt = $pdo->prepare("SELECT * FROM `langues` WHERE `equi_card` = :equi_card");
		$stmt->execute(['equi_card' => (int)$id]);
		return $stmt->fetch() ?: [];
	}
?>
