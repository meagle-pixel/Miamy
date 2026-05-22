<?php

include('functions.php');


if(isset($_GET['mod']))
{
	if (isset($_GET['mod']))
		$page = $_GET['mod'];
	else
		$page = 'home';
	
	$pageModel = new Page();
	$page_content = $pageModel->getByMod($page);
	
	$page_title = $page_content['nom'];
	$page_url = $page_content['url'];
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
