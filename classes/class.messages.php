<?php

	function getMessage($id)
	{
		$pdo  = Database::getInstance()->getConnection();
		$stmt = $pdo->prepare("SELECT * FROM `messages` WHERE `id` = ?");
		$stmt->execute([(int)$id]);
		$row = $stmt->fetch();
		return $row ?: false;
	}

	function setMessageRead($id)
	{
		$pdo  = Database::getInstance()->getConnection();
		$stmt = $pdo->prepare("UPDATE `messages` SET `unread` = '0' WHERE `id` = ?");
		$stmt->execute([(int)$id]);
	}

	function setMessageUnread($id)
	{
		$pdo  = Database::getInstance()->getConnection();
		$stmt = $pdo->prepare("UPDATE `messages` SET `unread` = '1' WHERE `id` = ?");
		$stmt->execute([(int)$id]);
	}

	function setMessageTrash($id)
	{
		$pdo  = Database::getInstance()->getConnection();
		$stmt = $pdo->prepare("UPDATE `messages` SET `delete` = '1' WHERE `id` = ?");
		$stmt->execute([(int)$id]);
	}

	function setMessageRecover($id)
	{
		$pdo  = Database::getInstance()->getConnection();
		$stmt = $pdo->prepare("UPDATE `messages` SET `delete` = '0' WHERE `id` = ?");
		$stmt->execute([(int)$id]);
	}

	function setMessageArchive($id)
	{
		$pdo  = Database::getInstance()->getConnection();
		$stmt = $pdo->prepare("UPDATE `messages` SET `delete` = '2' WHERE `id` = ?");
		$stmt->execute([(int)$id]);
	}

	function getMessagesReceivedFromUser($user_id, $desc = true)
	{
		$pdo   = Database::getInstance()->getConnection();
		$order = $desc ? 'DESC' : 'ASC';
		$stmt  = $pdo->prepare(
			"SELECT * FROM `messages` WHERE `destinataire` = ? AND `delete` = '0' ORDER BY `date` $order"
		);
		$stmt->execute([(int)$user_id]);
		$rows = $stmt->fetchAll();
		return count($rows) ? $rows : false;
	}

	function getMessagesReceivedUnreadFromUser($user_id)
	{
		$pdo  = Database::getInstance()->getConnection();
		$stmt = $pdo->prepare(
			"SELECT * FROM `messages` WHERE `destinataire` = ? AND `unread` = '1' AND `delete` = '0'"
		);
		$stmt->execute([(int)$user_id]);
		$rows = $stmt->fetchAll();
		return count($rows) ? $rows : false;
	}

	function getMessagesSentFromUser($user_id)
	{
		$pdo  = Database::getInstance()->getConnection();
		$stmt = $pdo->prepare(
			"SELECT * FROM `messages` WHERE `expediteur` = ? AND `delete` = '0'"
		);
		$stmt->execute([(int)$user_id]);
		$rows = $stmt->fetchAll();
		return count($rows) ? $rows : false;
	}

	function getMessagesDeleteFromUser($user_id)
	{
		$pdo  = Database::getInstance()->getConnection();
		$stmt = $pdo->prepare(
			"SELECT * FROM `messages` WHERE `destinataire` = ? AND `delete` = '1'"
		);
		$stmt->execute([(int)$user_id]);
		$rows = $stmt->fetchAll();
		return count($rows) ? $rows : false;
	}

?>
