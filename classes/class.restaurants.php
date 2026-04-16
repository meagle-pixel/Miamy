<?php
class Restaurant {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance()->getConnection();
    }

    public function listRestaurants($activeOnly = true, $featuredOnly = false) {
        $query      = "SELECT r.*, GROUP_CONCAT(c.name SEPARATOR ', ') as category_name
                       FROM `restaurants` r
                       LEFT JOIN `restaurant_categories` rc ON r.id_restaurant = rc.id_restaurant
                       LEFT JOIN `categories` c ON rc.id_categorie = c.id_categorie";
        $conditions = [];
        if ($activeOnly)   $conditions[] = "r.subscription_active = 1";
        if ($featuredOnly) $conditions[] = "r.is_featured = 1";

        if (count($conditions) > 0) {
            $query .= " WHERE " . implode(" AND ", $conditions);
        }
        $query .= " GROUP BY r.id_restaurant ORDER BY r.created_at DESC";

        return $this->pdo->query($query)->fetchAll();
    }

    public function getById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM `restaurants` WHERE `id_restaurant` = :id");
        $stmt->execute(['id' => (int)$id]);
        return $stmt->fetch();
    }

    public function getBySlug($slug) {
        $stmt = $this->pdo->prepare("SELECT * FROM `restaurants` WHERE `slug` = :slug");
        $stmt->execute(['slug' => $slug]);
        return $stmt->fetch();
    }

    public function update($id, $data) {
        $stmt = $this->pdo->prepare(
            "UPDATE `restaurants` SET
             `name` = :name, `city` = :city,
             `description` = :description, `is_featured` = :is_featured, `subscription_active` = :subscription_active
             WHERE `id_restaurant` = :id"
        );
        $result = $stmt->execute([
            'name'                => $data['name'],
            'city'                => $data['city'],
            'description'         => $data['description'],
            'is_featured'         => (int)$data['is_featured'],
            'subscription_active' => (int)$data['subscription_active'],
            'id'                  => (int)$id,
        ]);

        // Mise à jour de la catégorie via la table intermédiaire
        if ($result && isset($data['category_id'])) {
            $stmt_del = $this->pdo->prepare("DELETE FROM `restaurant_categories` WHERE `id_restaurant` = :id");
            $stmt_del->execute(['id' => (int)$id]);

            if ((int)$data['category_id'] > 0) {
                $stmt_cat = $this->pdo->prepare(
                    "INSERT INTO `restaurant_categories` (id_restaurant, id_categorie) VALUES (:id_restaurant, :id_categorie)"
                );
                $stmt_cat->execute([
                    'id_restaurant' => (int)$id,
                    'id_categorie'  => (int)$data['category_id'],
                ]);
            }
        }

        return $result;
    }

    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM `restaurants` WHERE `id_restaurant` = :id");
        return $stmt->execute(['id' => (int)$id]);
    }
}
