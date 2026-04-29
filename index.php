<?php

ob_start();
include('functions.php');


if (isset($_GET['mod'])) {
	$page = $_GET['mod'];

	$page_content = getPage($page);

	if (!empty($page_content['nom']) && !empty($page_content['url'])) {
		$page_title = $page_content['nom'];
		$page_url = $page_content['url'];
	} else {
		$page_title = 'Page introuvable';
		$page_url = 'views/404.php';
	}
} else {
	$page = '';
	$page_title = 'Accueil';
	$page_url = 'views/home.php';
}

// Une page est admin si son fichier est dans views/admin/
$is_admin_page = strpos($page_url, 'views/admin/') === 0;

if ($is_admin_page) {
	// Layout administrateur (template SB Admin)
	include('views/partials/admin_head.php');
	include($page_url);
	include('views/partials/admin_foot.php');
} else {
	// Layout public classique
	include('views/partials/head.php');
	include('views/partials/header.php');
	include($page_url);
	include('views/partials/footer.php');
	include('views/partials/foot.php');
}
