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
             WHERE `id_restaurant` = :id_restaurant
             ORDER BY FIELD(`categorie`, 'Entrées', 'Plats', 'Desserts', 'Boissons', 'Snacks'), `nom` ASC"
        );
        $stmt->execute(['id_restaurant' => (int)$id_restaurant]);
        return $stmt->fetchAll();
    }

    public function getById($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM `plats` WHERE `id` = :id");
        $stmt->execute(['id' => (int)$id]);
        return $stmt->fetch();
    }

    public function getCategoriesByRestaurant($id_restaurant)
    {
        $stmt = $this->pdo->prepare(
            "SELECT DISTINCT `categorie` FROM `plats` WHERE `id_restaurant` = :id_restaurant ORDER BY `categorie` ASC"
        );
        $stmt->execute(['id_restaurant' => (int)$id_restaurant]);
        return array_column($stmt->fetchAll(), 'categorie');
    }

    public function insert($data)
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO `plats` (`nom`, `description`, `prix`, `image`, `categorie`, `id_restaurant`, `disponible`, `created_at`)
             VALUES (:nom, :description, :prix, :image, :categorie, :id_restaurant, :disponible, NOW())"
        );
        $result = $stmt->execute([
            'nom'           => $data['nom'],
            'description'   => $data['description'],
            'prix'          => $data['prix'],
            'image'         => $data['image'],
            'categorie'     => $data['categorie'],
            'id_restaurant' => (int)$data['id_restaurant'],
            'disponible'    => (int)$data['disponible'],
        ]);
        return $result ? $this->pdo->lastInsertId() : false;
    }

    public function update($id, $data)
    {
        $stmt = $this->pdo->prepare(
            "UPDATE `plats` SET
                `nom` = :nom,
                `description` = :description,
                `prix` = :prix,
                `image` = :image,
                `categorie` = :categorie,
                `disponible` = :disponible
             WHERE `id` = :id"
        );
        return $stmt->execute([
            'nom'         => $data['nom'],
            'description' => $data['description'],
            'prix'        => $data['prix'],
            'image'       => $data['image'],
            'categorie'   => $data['categorie'],
            'disponible'  => (int)$data['disponible'],
            'id'          => (int)$id,
        ]);
    }

    public function delete($id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM `plats` WHERE `id` = :id");
        return $stmt->execute(['id' => (int)$id]);
    }

    public function toggleDisponible($id)
    {
        $stmt = $this->pdo->prepare(
            "UPDATE `plats` SET `disponible` = IF(`disponible` = 1, 0, 1) WHERE `id` = :id"
        );
        return $stmt->execute(['id' => (int)$id]);
    }


    public function getDerniersPlats($id_restaurant, $limite = 3)
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM `plats` 
         WHERE `id_restaurant` = :id_restaurant 
         ORDER BY `created_at` DESC 
         LIMIT :limite"
        );
        $stmt->bindValue(':id_restaurant', (int)$id_restaurant, \PDO::PARAM_INT);
        $stmt->bindValue(':limite', (int)$limite, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
