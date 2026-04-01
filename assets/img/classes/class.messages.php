<?php

	function getMessage($id)
	{
		$messages = array();
		$db = Database::getInstance();
		$mysqli = $db->getConnection(); 
		$mysqli->set_charset( 'utf8');
		$query = "SELECT * FROM `messages` WHERE `id` = '".$id."'";
		
		if($result = $mysqli->query($query))
		{
			while ($messagetmp = $result->fetch_assoc()) { $messages = $messagetmp; }
			$result->free();
		}
		
		if(count($messages))
			return $messages;
		else
			return false;
	}

	function setMessageRead($id)
	{
		$db = Database::getInstance();
		$mysqli = $db->getConnection(); 
		
		$query =  "UPDATE `messages` SET
				  `unread` = '0'
				  WHERE `id` = '".$id."'";
		
		$mysqli->query($query);
	}
	
	function setMessageUnread($id)
	{
		$db = Database::getInstance();
		$mysqli = $db->getConnection(); 
		
		$query =  "UPDATE `messages` SET
				  `unread` = '1'
				  WHERE `id` = '".$id."'";
		
		$mysqli->query($query);
	}
	
	function setMessageTrash($id)
	{
		$db = Database::getInstance();
		$mysqli = $db->getConnection(); 
		
		$query =  "UPDATE `messages` SET
				  `delete` = '1'
				  WHERE `id` = '".$id."'";
		
		$mysqli->query($query);
	}
	
	function setMessageRecover($id)
	{
		$db = Database::getInstance();
		$mysqli = $db->getConnection(); 
		
		$query =  "UPDATE `messages` SET
				  `delete` = '0'
				  WHERE `id` = '".$id."'";
		
		$mysqli->query($query);
	}
	
	function setMessageArchive($id)
	{
		$db = Database::getInstance();
		$mysqli = $db->getConnection(); 
		
		$query =  "UPDATE `messages` SET
				  `delete` = '2'
				  WHERE `id` = '".$id."'";
		
		$mysqli->query($query);
	}

	function getMessagesReceivedFromUser($user_id,$desc=true)
	{
		$messages = array();
		$db = Database::getInstance();
		$mysqli = $db->getConnection(); 
		$mysqli->set_charset( 'utf8');
		if($desc)
			$query = "SELECT * FROM `messages` WHERE `destinataire` = '".$user_id."' AND `delete` = '0' ORDER BY `date` DESC";
		else
			$query = "SELECT * FROM `messages` WHERE `destinataire` = '".$user_id."' AND `delete` = '0' ORDER BY `date` ASC";
		
		if($result = $mysqli->query($query))
		{
			while ($messagetmp = $result->fetch_assoc()) { $messages[] = $messagetmp; }
			$result->free();
		}
		
		if(count($messages))
			return $messages;
		else
			return false;
	}
	
	function getMessagesReceivedUnreadFromUser($user_id)
	{
		$messages = array();
		$db = Database::getInstance();
		$mysqli = $db->getConnection(); 
		$mysqli->set_charset( 'utf8');
		$query = "SELECT * FROM `messages` WHERE `destinataire` = '".$user_id."' AND `unread` = '1' AND `delete` = '0'";
		
		if($result = $mysqli->query($query))
		{
			while ($messagetmp = $result->fetch_assoc()) { $messages[] = $messagetmp; }
			$result->free();
		}
		
		if(count($messages))
			return $messages;
		else
			return false;
	}
	
	function getMessagesSentFromUser($user_id)
	{
		$messages = array();
		$db = Database::getInstance();
		$mysqli = $db->getConnection(); 
		$mysqli->set_charset( 'utf8');
		$query = "SELECT * FROM `messages` WHERE `expediteur` = '".$user_id."' AND `delete` = '0'";
		
		if($result = $mysqli->query($query))
		{
			while ($messagetmp = $result->fetch_assoc()) { $messages[] = $messagetmp; }
			$result->free();
		}
		
		if(count($messages))
			return $messages;
		else
			return false;
	}
	
	function getMessagesDeleteFromUser($user_id)
	{
		$messages = array();
		$db = Database::getInstance();
		$mysqli = $db->getConnection(); 
		$mysqli->set_charset( 'utf8');
		$query = "SELECT * FROM `messages` WHERE `destinataire` = '".$user_id."' AND `delete` = '1'";
		
		if($result = $mysqli->query($query))
		{
			while ($messagetmp = $result->fetch_assoc()) { $messages[] = $messagetmp; }
			$result->free();
		}
		
		if(count($messages))
			return $messages;
		else
			return false;
	}
	

?>