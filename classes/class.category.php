<?php
class Category {
    private $db;
    private $mysqli;

    public function __construct() {
        $this->db = Database::getInstance();
        $this->mysqli = $this->db->getConnection();
        $this->mysqli->set_charset('utf8');
    }

    public function listAll() {
        $categories = [];
        $query = "SELECT * FROM `categories` ORDER BY `name` ASC";
        if ($result = $this->mysqli->query($query)) {
            while ($row = $result->fetch_assoc()) { $categories[] = $row; }
            $result->free();
        }
        return $categories;
    }

    public function getById($id) {
        $stmt = $this->mysqli->prepare("SELECT * FROM `categories` WHERE `id` = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function insert($name, $icon = null) {
        $stmt = $this->mysqli->prepare("INSERT INTO `categories` (`name`, `icon`) VALUES (?, ?)");
        $stmt->bind_param("ss", $name, $icon);
        $stmt->execute();
        return $this->mysqli->insert_id;
    }

    public function delete($id) {
        $stmt = $this->mysqli->prepare("DELETE FROM `categories` WHERE `id` = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}