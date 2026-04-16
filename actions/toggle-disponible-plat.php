<?php
header('Content-Type: application/json');

chdir(dirname(__DIR__));
require_once('functions.php');

// Sécurité : doit être connecté en tant que restaurateur
if (!isset($_SESSION['connected']) || $_SESSION['connected'] !== true || $_SESSION['user']['profil'] > 2) {
    echo json_encode(['success' => false, 'message' => 'Non autorisé']);
    exit();
}

$id_plat         = isset($_POST['id_plat']) ? (int)$_POST['id_plat'] : 0;
$id_restaurateur = (int)$_SESSION['user']['profil_id'];

if (!$id_plat) {
    echo json_encode(['success' => false, 'message' => 'ID invalide']);
    exit();
}

// Vérifier que le plat appartient bien à un restaurant de ce restaurateur
$pdo  = Database::getInstance()->getConnection();
$stmt = $pdo->prepare(
    "SELECT p.id, p.disponible FROM plats p
     JOIN restaurants r ON r.id_restaurant = p.id_restaurant
     WHERE p.id = ? AND r.id_restaurateur = ?"
);
$stmt->execute([$id_plat, $id_restaurateur]);
$plat = $stmt->fetch();

if (!$plat) {
    echo json_encode(['success' => false, 'message' => 'Plat introuvable ou accès refusé']);
    exit();
}

// Toggle : inverse la valeur de disponible
$platClass = new Plat();
$ok        = $platClass->toggleDisponible($id_plat);

// Retourner le nouvel état (inverse de l'ancien)
$nouvelEtat = $plat['disponible'] ? 0 : 1;

echo json_encode(['success' => (bool)$ok, 'disponible' => $nouvelEtat]);
