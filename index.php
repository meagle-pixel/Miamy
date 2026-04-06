<?php

ob_start();
include('functions.php');


if(isset($_GET['mod']))
{
	$page = $_GET['mod'];

	$page_content = getPage($page);

	if(!empty($page_content['nom']) && !empty($page_content['url']))
	{
		$page_title = $page_content['nom'];
		$page_url = $page_content['url'];
	}
	else
	{
		$page_title = 'Page introuvable';
		$page_url = 'views/404.php';
	}
}
else
{
	$page_title = 'Accueil';
	$page_url = 'views/home.php';
}

include('views/partials/head.php'); 
include('views/partials/header.php');

include($page_url); 

include('views/partials/footer.php');
include('views/partials/foot.php'); 

?>
