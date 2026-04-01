<?php

	$messages = array();
	$messagesunread = array();
	$messagessent = array();
	$messagesdelete = array();

	if(isset($_SESSION['admin_user']['id']))
	{
		$messages = getMessagesReceivedFromUser($_SESSION['admin_user']['id']);
		$messagesunread = getMessagesReceivedUnreadFromUser($_SESSION['admin_user']['id']);
		$messagessent = getMessagesSentFromUser($_SESSION['admin_user']['id']);
		$messagesdelete = getMessagesDeleteFromUser($_SESSION['admin_user']['id']);
	}
	
	$nbmessages = 0;
	$nbmessagesunread = 0;
	$nbmessagessent = 0;
	$nbmessagesdelete = 0;
	
	if(is_array($messages))
		$nbmessages = count($messages);
	if(is_array($messagesunread))
		$nbmessagesunread = count($messagesunread);
	if(is_array($messagessent))
		$nbmessagessent = count($messagessent);
	if(is_array($messagesdelete))
		$nbmessagesdelete = count($messagesdelete);
	
	$current_message = false;
	
	if(isset($_GET['id']))
	{
		$id = $_GET['id'];
		$current_message = getMessage($id);
		setMessageRead($id);
	}
	
	$exp = $current_message['expediteur'];
	
	$utilisateur = getUser($exp);
	
?>