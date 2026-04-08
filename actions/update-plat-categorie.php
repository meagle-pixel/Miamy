<?php
header('Content-Type: application/json');

// Bootstrap : on se place à la racine pour que les require_once de functions.php fonctionnent
chdir(dirname(__DIR__));
require_once('functions.php');

// Sécurité : doit être connecté en tant que restaurateur (profil <= 2)
if (!isset($_SESSION['connected']) || $_SESSION['connected'] !== true || $_SESSION['user']['profil'] > 2) {
    echo json_encode(['success' => false, 'message' => 'Non autorisé']);
    exit();
}

$id_plat         = isset($_POST['id_plat'])   ? (int)trim($_POST['id_plat'])   : 0;
$nouvelle_cat    = isset($_POST['categorie']) ? trim($_POST['categorie'])       : '';
$id_restaurateur = (int)$_SESSION['user']['profil_id'];

// Catégories autorisées (alignées avec class.plats.php)
$categories_valides = ['Entrées', 'Plats', 'Desserts', 'Boissons', 'Snacks'];

if (!$id_plat || !in_array($nouvelle_cat, $categories_valides, true)) {
    echo json_encode(['success' => false, 'message' => 'Données invalides']);
    exit();
}

// Vérifier que le plat appartient bien à un restaurant de ce restaurateur
$pdo  = Database::getInstance()->getConnection();
$stmt = $pdo->prepare(
    "SELECT p.id FROM plats p
     JOIN restaurants r ON r.id = p.id_restaurant
     WHERE p.id = :id_plat AND r.id_restaurateur = :id_restaurateur"
);
$stmt->execute([
    'id_plat'         => $id_plat,
    'id_restaurateur' => $id_restaurateur,
]);

if (!$stmt->fetch()) {
    echo json_encode(['success' => false, 'message' => 'Plat introuvable ou accès refusé']);
    exit();
}

// Mettre à jour la catégorie
$stmt = $pdo->prepare("UPDATE plats SET categorie = :categorie WHERE id = :id");
$ok   = $stmt->execute([
    'categorie' => $nouvelle_cat,
    'id'        => $id_plat,
]);

echo json_encode(['success' => (bool)$ok]);
