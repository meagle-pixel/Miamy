<?php
class Restaurant {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance()->getConnection();
    }

    public function listRestaurants($activeOnly = true, $featuredOnly = false) {
        $query      = "SELECT r.*, c.name as category_name
                       FROM `restaurants` r
                       LEFT JOIN `categories` c ON r.category_id = c.id";
        $conditions = [];
        if ($activeOnly)   $conditions[] = "r.subscription_active = 1";
        if ($featuredOnly) $conditions[] = "r.is_featured = 1";

        if (count($conditions) > 0) {
            $query .= " WHERE " . implode(" AND ", $conditions);
        }
        $query .= " ORDER BY r.created_at DESC";

        return $this->pdo->query($query)->fetchAll();
    }

    public function getById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM `restaurants` WHERE `id` = :id");
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
             `name` = :name, `city` = :city, `category_id` = :category_id,
             `description` = :description, `is_featured` = :is_featured, `subscription_active` = :subscription_active
             WHERE `id` = :id"
        );
        return $stmt->execute([
            'name'                => $data['name'],
            'city'                => $data['city'],
            'category_id'         => (int)$data['category_id'],
            'description'         => $data['description'],
            'is_featured'         => (int)$data['is_featured'],
            'subscription_active' => (int)$data['subscription_active'],
            'id'                  => (int)$id,
        ]);
    }

    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM `restaurants` WHERE `id` = :id");
        return $stmt->execute(['id' => (int)$id]);
    }
}
