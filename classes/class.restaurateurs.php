<?php
/* ==========================================================================
   GESTION DES RESTAURATEURS (MULTI-ÉTABLISSEMENTS)
   ========================================================================== */

function getRestaurateur($id) {
    $pdo  = Database::getInstance()->getConnection();
    $stmt = $pdo->prepare("SELECT * FROM restaurateurs WHERE id = :id");
    $stmt->execute(['id' => (int)$id]);
    return $stmt->fetch() ?: null;
}

function getRestaurantsByOwner($id_restaurateur) {
    $pdo  = Database::getInstance()->getConnection();
    $stmt = $pdo->prepare("SELECT * FROM restaurants WHERE id_restaurateur = :id_restaurateur ORDER BY name ASC");
    $stmt->execute(['id_restaurateur' => (int)$id_restaurateur]);
    return $stmt->fetchAll();
}

function insertRestaurateur($data) {
    $pdo  = Database::getInstance()->getConnection();
    $stmt = $pdo->prepare(
        "INSERT INTO restaurateurs (nom, prenom, email, telephone, dateinscription)
         VALUES (:nom, :prenom, :email, :telephone, NOW())"
    );
    $stmt->execute([
        'nom'       => $data['nom'],
        'prenom'    => $data['prenom'],
        'email'     => $data['email'],
        'telephone' => $data['telephone'],
    ]);
    return $pdo->lastInsertId();
}

function updateRestaurateur($data) {
    $pdo  = Database::getInstance()->getConnection();
    $stmt = $pdo->prepare(
        "UPDATE restaurateurs SET
            nom = :nom,
            prenom = :prenom,
            email = :email,
            telephone = :telephone
         WHERE id = :id"
    );
    return $stmt->execute([
        'nom'       => $data['nom'],
        'prenom'    => $data['prenom'],
        'email'     => $data['email'],
        'telephone' => $data['telephone'],
        'id'        => (int)$data['id'],
    ]);
}
