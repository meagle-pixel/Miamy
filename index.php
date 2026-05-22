<?php

ob_start();
include('functions.php');

// Chargement des controllers
require_once('controllers/AdminController.php');
require_once('controllers/AuthController.php');
require_once('controllers/HomeController.php');
require_once('controllers/PlatController.php');
require_once('controllers/RestaurantController.php');
require_once('controllers/UserController.php');

// Table de routage : page -> [controller, methode]
$dispatchMap = [
	'accueil'                 => [new HomeController(),       'index'],
	'connexion'               => [new AuthController(),       'login'],
	'deconnexion'             => [new AuthController(),       'logout'],
	'inscription'             => [new AuthController(),       'register'],
	'inscription-client'      => [new AuthController(),       'registerClient'],
	'dashboard'               => [new AdminController(),      'dashboard'],
	'admin-panel'             => [new AdminController(),      'panel'],
	'admin-restaurants'       => [new AdminController(),      'restaurants'],
	'ajouter-admin'           => [new AdminController(),      'ajouterAdmin'],
	'liste-plats'             => [new PlatController(),        'liste'],
	'gestion-carte'           => [new PlatController(),        'gestionCarte'],
	'ajouter-plat'            => [new PlatController(),        'ajouter'],
	'modifier-plat'           => [new PlatController(),        'modifier'],
	'supprimer-plat'          => [new PlatController(),        'supprimer'],
	'toggle-disponible-plat'  => [new PlatController(),        'toggleDisponible'],
	'update-plat-categorie'   => [new PlatController(),        'updateCategorie'],
	'liste-restaurants'       => [new RestaurantController(),  'liste'],
	'ajouter-restaurant'      => [new RestaurantController(),  'ajouter'],
	'modifier-restaurant'     => [new RestaurantController(),  'modifier'],
	'supprimer-restaurant'    => [new RestaurantController(),  'supprimer'],
	'details'                 => [new RestaurantController(),  'details'],
	'save-horaires'           => [new RestaurantController(),  'saveHoraires'],
	'mon-compte'              => [new UserController(),        'monCompte'],
	'mon-compte-restaurateur' => [new UserController(),        'monCompteRestaurateur'],
	'profil-editer'           => [new UserController(),        'profilEditer'],
	'profile'                 => [new UserController(),        'profile'],
];

// On considere "pas de mod" comme la page d'accueil
$page = isset($_GET['mod']) ? $_GET['mod'] : 'accueil';

// Si la page a un controller, on le dispatch et on extrait ses donnees
if (isset($dispatchMap[$page])) {
	[$controller, $method] = $dispatchMap[$page];
	$viewData = $controller->$method(); // peut faire header()+exit() ou retourner un tableau
	if (is_array($viewData)) {
		extract($viewData); // rend les variables disponibles pour la vue
	}
}

$pageModel = new Page();
$page_content = $pageModel->getByMod($page);

if (!empty($page_content['nom']) && !empty($page_content['url'])) {
	$page_title = $page_content['nom'];
	$page_url   = $page_content['url'];
} else {
	$page_title = 'Page introuvable';
	$page_url   = 'views/404.php';
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
