<?php
	session_start();
	
	include('functions.php');
	
	if($_SESSION['admin_connected']==false)
		header('Location: login.php');
	
	include('admin.php');

?> 