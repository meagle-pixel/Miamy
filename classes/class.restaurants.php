<?php
class Restaurant {
    private $mysqli;

    public function __construct() {
        $this->mysqli = Database::getInstance()->getConnection();
        $this->mysqli->set_charset('utf8');
    }

    // Liste les restaurants (avec filtres optionnels)
    public function listRestaurants($activeOnly = true, $featuredOnly = false) {
        $restaurants = [];
        $query = "SELECT r.*, c.name as category_name 
                  FROM `restaurants` r 
                  LEFT JOIN `categories` c ON r.category_id = c.id";
        
        $conditions = [];
        if ($activeOnly) $conditions[] = "r.subscription_active = 1";
        if ($featuredOnly) $conditions[] = "r.is_featured = 1";
        
        if (count($conditions) > 0) {
            $query .= " WHERE " . implode(" AND ", $conditions);
        }
        
        $query .= " ORDER BY r.created_at DESC";

        if ($result = $this->mysqli->query($query)) {
            while ($row = $result->fetch_assoc()) { $restaurants[] = $row; }
            $result->free();
        }
        return $restaurants;
    }

    public function getById($id) {
        $stmt = $this->mysqli->prepare("SELECT * FROM `restaurants` WHERE `id` = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function getBySlug($slug) {
        $stmt = $this->mysqli->prepare("SELECT * FROM `restaurants` WHERE `slug` = ?");
        $stmt->bind_param("s", $slug);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function update($id, $data) {
        $query = "UPDATE `restaurants` SET 
                  `name` = ?, `city` = ?, `category_id` = ?, 
                  `description` = ?, `is_featured` = ?, `subscription_active` = ? 
                  WHERE `id` = ?";
        
        $stmt = $this->mysqli->prepare($query);
        $stmt->bind_param("ssisiii", 
            $data['name'], $data['city'], $data['category_id'], 
            $data['description'], $data['is_featured'], $data['subscription_active'], 
            $id
        );
        return $stmt->execute();
    }

    public function delete($id) {
        $stmt = $this->mysqli->prepare("DELETE FROM `restaurants` WHERE `id` = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}