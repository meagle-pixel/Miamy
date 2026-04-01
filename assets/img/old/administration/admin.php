<?php

	ob_start();

	if (isset($_REQUEST['mod']))
		$page = $_REQUEST['mod'];
	else
		$page = 'dashboard';

	$page_content = getPage($page,$_SESSION['admin_user']['profile']);

	$page_title = 'Accueil administration';
 
	if(file_exists('controllers/'.$page.'.php'))
		include('controllers/'.$page.'.php');
 
	include('templates/head.php');

	include('templates/header.php'); 
	
	include('templates/'.$page_content);
	
	include('templates/footer.php'); 
	
	include('templates/scripts.php'); 
	
	include('templates/foot.php'); 
	
?>