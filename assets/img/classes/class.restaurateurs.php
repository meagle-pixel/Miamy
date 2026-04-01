<?php 
/* ==========================================================================
   GESTION DES RESTAURATEURS (MULTI-ÉTABLISSEMENTS)
   ========================================================================== */

/**
 * Récupérer un restaurateur par son ID
 */
function getRestaurateur($id) {
    $db = Database::getInstance();
    $mysqli = $db->getConnection();
    $mysqli->set_charset('utf8');
    $id = (int)$id;
    
    $query = "SELECT * FROM restaurateurs WHERE id = $id";
    $res = $mysqli->query($query);
    
    return $res ? $res->fetch_assoc() : null;
}

/**
 * Récupérer tous les restaurants appartenant à un restaurateur précis
 */
function getRestaurantsByOwner($id_restaurateur) {
    $db = Database::getInstance();
    $mysqli = $db->getConnection();
    $mysqli->set_charset('utf8');
    $id = (int)$id_restaurateur;
    
    $query = "SELECT * FROM restaurants WHERE id_restaurateur = $id ORDER BY name ASC";
    $results = [];
    
    if($res = $mysqli->query($query)) {
        while($row = $res->fetch_assoc()) {
            $results[] = $row;
        }
        $res->free();
    }
    return $results;
}

/**
 * Insérer un nouveau restaurateur (sans lier de resto ici)
 */
function insertRestaurateur($data) {
    $db = Database::getInstance();
    $mysqli = $db->getConnection();
    $mysqli->set_charset('utf8');
    
    $nom = $mysqli->real_escape_string($data['nom']);
    $prenom = $mysqli->real_escape_string($data['prenom']);
    $email = $mysqli->real_escape_string($data['email']);
    $telephone = $mysqli->real_escape_string($data['telephone']);
    
    $query = "INSERT INTO restaurateurs (nom, prenom, email, telephone, dateinscription) 
              VALUES ('$nom', '$prenom', '$email', '$telephone', NOW())";
    
    $mysqli->query($query);
    return $mysqli->insert_id;
}

/**
 * Modifier un restaurateur
 */
function updateRestaurateur($data) {
    $db = Database::getInstance();
    $mysqli = $db->getConnection();
    $mysqli->set_charset('utf8');
    
    $id = (int)$data['id'];
    $nom = $mysqli->real_escape_string($data['nom']);
    $prenom = $mysqli->real_escape_string($data['prenom']);
    $email = $mysqli->real_escape_string($data['email']);
    $telephone = $mysqli->real_escape_string($data['telephone']);
    
    $query = "UPDATE restaurateurs SET 
                nom = '$nom', 
                prenom = '$prenom', 
                email = '$email', 
                telephone = '$telephone' 
              WHERE id = $id";
    
    return $mysqli->query($query);
}