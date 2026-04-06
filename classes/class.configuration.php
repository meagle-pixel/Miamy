<?php

	function getConfigurationById($id)
	{
		$pdo  = Database::getInstance()->getConnection();
		$stmt = $pdo->prepare("SELECT * FROM `configuration` WHERE `id` = ?");
		$stmt->execute([(int)$id]);
		return $stmt->fetch() ?: [];
	}

	function getConfigurationByName($name)
	{
		$pdo  = Database::getInstance()->getConnection();
		$stmt = $pdo->prepare("SELECT * FROM `configuration` WHERE `name` = ?");
		$stmt->execute([$name]);
		$row = $stmt->fetch();
		return $row['value'] ?? null;
	}

	function getFullConfiguration()
	{
		$pdo = Database::getInstance()->getConnection();
		return $pdo->query("SELECT * FROM `configuration` ORDER BY `order` ASC")->fetchAll();
	}

?>
