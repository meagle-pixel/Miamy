<?php
chdir(dirname(__DIR__));
require_once('functions.php');

// Sécurité : restaurateur connecté uniquement
if (!isset($_SESSION['connected']) || $_SESSION['connected'] !== true || $_SESSION['user']['profil'] > 2) {
    header('Location: ' . $GLOBALS['url'] . '/connexion');
    exit();
}

$id_restaurant   = isset($_POST['id_restaurant']) ? (int)$_POST['id_restaurant'] : 0;
$id_restaurateur = (int)$_SESSION['user']['profil_id'];

if (!$id_restaurant) {
    header('Location: ' . $GLOBALS['url'] . '/mon-compte-restaurateur');
    exit();
}

// Vérifier que le restaurant appartient bien à ce restaurateur
$pdo  = Database::getInstance()->getConnection();
$stmt = $pdo->prepare("SELECT id_restaurant FROM restaurants WHERE id_restaurant = ? AND id_restaurateur = ?");
$stmt->execute([$id_restaurant, $id_restaurateur]);

if (!$stmt->fetch()) {
    header('Location: ' . $GLOBALS['url'] . '/mon-compte-restaurateur');
    exit();
}

// Sauvegarde
$data          = $_POST['horaires'] ?? [];
$horairesClass = new Horaires();
$ok            = $horairesClass->save($id_restaurant, $data);

$status = $ok ? 'ok' : 'error';
header('Location: ' . $GLOBALS['url'] . '/details?id=' . $id_restaurant . '&horaires=' . $status);
exit();
