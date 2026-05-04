<?php

ob_start();
include('functions.php');

// Chargement des controllers
require_once('controllers/PlatController.php');
require_once('controllers/RestaurantController.php');
require_once('controllers/UserController.php');

// Table de routage : page → [controller, méthode]
$dispatchMap = [
    'liste-plats'             => [new PlatController(),        'liste'],
    'gestion-carte'           => [new PlatController(),        'gestionCarte'],
    'ajouter-plat'            => [new PlatController(),        'ajouter'],
    'modifier-plat'           => [new PlatController(),        'modifier'],
    'supprimer-plat'          => [new PlatController(),        'supprimer'],
    'liste-restaurants'       => [new RestaurantController(),  'liste'],
    'ajouter-restaurant'      => [new RestaurantController(),  'ajouter'],
    'modifier-restaurant'     => [new RestaurantController(),  'modifier'],
    'supprimer-restaurant'    => [new RestaurantController(),  'supprimer'],
    'mon-compte'              => [new UserController(),        'monCompte'],
    'mon-compte-restaurateur' => [new UserController(),        'monCompteRestaurateur'],
    'profil-editer'           => [new UserController(),        'profilEditer'],
    'profile'                 => [new UserController(),        'profile'],
];

if (isset($_GET['mod'])) {
	$page = $_GET['mod'];

	// Si la page a un controller, on le dispatch et on extrait ses données
	if (isset($dispatchMap[$page])) {
		[$controller, $method] = $dispatchMap[$page];
		$viewData = $controller->$method(); // peut faire header()+exit() ou retourner un tableau
		if (is_array($viewData)) {
			extract($viewData); // rend les variables disponibles pour la vue
		}
	}

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
