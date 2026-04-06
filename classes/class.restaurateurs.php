<?php
/* ==========================================================================
   GESTION DES RESTAURATEURS (MULTI-ÉTABLISSEMENTS)
   ========================================================================== */

function getRestaurateur($id) {
    $pdo  = Database::getInstance()->getConnection();
    $stmt = $pdo->prepare("SELECT * FROM restaurateurs WHERE id = ?");
    $stmt->execute([(int)$id]);
    return $stmt->fetch() ?: null;
}

function getRestaurantsByOwner($id_restaurateur) {
    $pdo  = Database::getInstance()->getConnection();
    $stmt = $pdo->prepare("SELECT * FROM restaurants WHERE id_restaurateur = ? ORDER BY name ASC");
    $stmt->execute([(int)$id_restaurateur]);
    return $stmt->fetchAll();
}

function insertRestaurateur($data) {
    $pdo  = Database::getInstance()->getConnection();
    $stmt = $pdo->prepare(
        "INSERT INTO restaurateurs (nom, prenom, email, telephone, dateinscription)
         VALUES (?, ?, ?, ?, NOW())"
    );
    $stmt->execute([
        $data['nom'],
        $data['prenom'],
        $data['email'],
        $data['telephone'],
    ]);
    return $pdo->lastInsertId();
}

function updateRestaurateur($data) {
    $pdo  = Database::getInstance()->getConnection();
    $stmt = $pdo->prepare(
        "UPDATE restaurateurs SET
            nom = ?,
            prenom = ?,
            email = ?,
            telephone = ?
         WHERE id = ?"
    );
    return $stmt->execute([
        $data['nom'],
        $data['prenom'],
        $data['email'],
        $data['telephone'],
        (int)$data['id'],
    ]);
}
