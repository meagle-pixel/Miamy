<?php
class Plat {
    private $mysqli;

    public function __construct() {
        $this->mysqli = Database::getInstance()->getConnection();
        $this->mysqli->set_charset('utf8mb4');
    }

    // Liste tous les plats d'un restaurant
    public function getByRestaurant($id_restaurant) {
        $plats = [];
        $stmt = $this->mysqli->prepare(
            "SELECT * FROM `plats` WHERE `id_restaurant` = ? ORDER BY `categorie` ASC, `nom` ASC"
        );
        $stmt->bind_param("i", $id_restaurant);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $plats[] = $row;
        }
        $stmt->close();
        return $plats;
    }

    // Récupère un plat par son ID
    public function getById($id) {
        $stmt = $this->mysqli->prepare("SELECT * FROM `plats` WHERE `id` = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $plat = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $plat;
    }

    // Liste les catégories distinctes d'un restaurant
    public function getCategoriesByRestaurant($id_restaurant) {
        $categories = [];
        $stmt = $this->mysqli->prepare(
            "SELECT DISTINCT `categorie` FROM `plats` WHERE `id_restaurant` = ? ORDER BY `categorie` ASC"
        );
        $stmt->bind_param("i", $id_restaurant);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $categories[] = $row['categorie'];
        }
        $stmt->close();
        return $categories;
    }

    // Ajoute un plat
    public function insert($data) {
        $stmt = $this->mysqli->prepare(
            "INSERT INTO `plats` (`nom`, `description`, `prix`, `image`, `categorie`, `id_restaurant`, `disponible`, `created_at`)
             VALUES (?, ?, ?, ?, ?, ?, ?, NOW())"
        );
        $stmt->bind_param(
            "ssdssii",
            $data['nom'],
            $data['description'],
            $data['prix'],
            $data['image'],
            $data['categorie'],
            $data['id_restaurant'],
            $data['disponible']
        );
        $result = $stmt->execute();
        $insert_id = $this->mysqli->insert_id;
        $stmt->close();
        return $result ? $insert_id : false;
    }

    // Modifie un plat
    public function update($id, $data) {
        $stmt = $this->mysqli->prepare(
            "UPDATE `plats` SET
                `nom` = ?,
                `description` = ?,
                `prix` = ?,
                `image` = ?,
                `categorie` = ?,
                `disponible` = ?
             WHERE `id` = ?"
        );
        $stmt->bind_param(
            "ssdsiii",
            $data['nom'],
            $data['description'],
            $data['prix'],
            $data['image'],
            $data['categorie'],
            $data['disponible'],
            $id
        );
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    // Supprime un plat
    public function delete($id) {
        $stmt = $this->mysqli->prepare("DELETE FROM `plats` WHERE `id` = ?");
        $stmt->bind_param("i", $id);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    // Active ou désactive la disponibilité d'un plat
    public function toggleDisponible($id) {
        $stmt = $this->mysqli->prepare(
            "UPDATE `plats` SET `disponible` = IF(`disponible` = 1, 0, 1) WHERE `id` = ?"
        );
        $stmt->bind_param("i", $id);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }
}
