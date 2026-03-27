<?php
	session_start();
	
	include('../functions.php');
	
	if($_SESSION['connected']==false)
		header('Location: ../login.php');
	
	$_SESSION['lang'] = $_POST['region'];
	
?> 