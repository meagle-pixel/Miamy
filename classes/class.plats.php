<?php
class Plat
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getConnection();
    }

    public function getByRestaurant($id_restaurant)
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM `plats`
             WHERE `id_restaurant` = ?
             ORDER BY FIELD(`categorie`, 'Entrées', 'Plats', 'Desserts', 'Boissons', 'Snacks'), `nom` ASC"
        );
        $stmt->execute([(int)$id_restaurant]);
        return $stmt->fetchAll();
    }

    public function getById($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM `plats` WHERE `id` = ?");
        $stmt->execute([(int)$id]);
        return $stmt->fetch();
    }

    public function getCategoriesByRestaurant($id_restaurant)
    {
        $stmt = $this->pdo->prepare(
            "SELECT DISTINCT `categorie` FROM `plats` WHERE `id_restaurant` = ? ORDER BY `categorie` ASC"
        );
        $stmt->execute([(int)$id_restaurant]);
        return array_column($stmt->fetchAll(), 'categorie');
    }

    public function insert($data)
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO `plats` (`nom`, `description`, `prix`, `image`, `categorie`, `id_restaurant`, `disponible`, `created_at`)
             VALUES (?, ?, ?, ?, ?, ?, ?, NOW())"
        );
        $result = $stmt->execute([
            $data['nom'],
            $data['description'],
            $data['prix'],
            $data['image'],
            $data['categorie'],
            (int)$data['id_restaurant'],
            (int)$data['disponible'],
        ]);
        return $result ? $this->pdo->lastInsertId() : false;
    }

    public function update($id, $data)
    {
        $stmt = $this->pdo->prepare(
            "UPDATE `plats` SET
                `nom` = ?,
                `description` = ?,
                `prix` = ?,
                `image` = ?,
                `categorie` = ?,
                `disponible` = ?
             WHERE `id` = ?"
        );
        return $stmt->execute([
            $data['nom'],
            $data['description'],
            $data['prix'],
            $data['image'],
            $data['categorie'],
            (int)$data['disponible'],
            (int)$id,
        ]);
    }

    public function delete($id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM `plats` WHERE `id` = ?");
        return $stmt->execute([(int)$id]);
    }

    public function toggleDisponible($id)
    {
        $stmt = $this->pdo->prepare(
            "UPDATE `plats` SET `disponible` = IF(`disponible` = 1, 0, 1) WHERE `id` = ?"
        );
        return $stmt->execute([(int)$id]);
    }
}
