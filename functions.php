<?php

session_start();

// Fuseau horaire global PHP : tout date()/strtotime() raisonne en heure de Paris.
date_default_timezone_set('Europe/Paris');

require_once('config.php');

if (APP_DEV == true) {
	error_reporting(E_ALL);
	ini_set('display_errors', 'on');
} else {
	error_reporting(0);
	ini_set('display_errors', 'off');
}

require_once('classes/class.clients.php');
require_once('classes/class.database.php');
require_once('classes/class.functions.php');
require_once('classes/class.pages.php');
require_once('classes/class.userlogs.php');
require_once('classes/class.imageuploader.php');
require_once('classes/class.users.php');
require_once('classes/class.restaurants.php');
require_once('classes/class.restaurateurs.php');
require_once('classes/class.category.php');
require_once('classes/class.plats.php');
require_once('classes/class.horaires.php');
