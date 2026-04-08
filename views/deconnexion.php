<?php

// Log de déconnexion si connecté
if (isset($_SESSION['user']['id'])) {
    logUserAction($_SESSION['user']['id'], 'logout', 'Déconnexion du site');
}

// Destruction de la session
$_SESSION = array();
session_destroy();

// Redirection vers l'accueil
header('Location: ' . $GLOBALS['url'] . '/accueil');
exit();

?>