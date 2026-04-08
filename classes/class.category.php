<?php
class Category {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance()->getConnection();
    }

    public function listAll() {
        return $this->pdo->query("SELECT * FROM `categories` ORDER BY `name` ASC")->fetchAll();
    }

    public function getById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM `categories` WHERE `id` = :id");
        $stmt->execute(['id' => (int)$id]);
        return $stmt->fetch();
    }

    public function insert($name, $icon = null) {
        $stmt = $this->pdo->prepare("INSERT INTO `categories` (`name`, `icon`) VALUES (:name, :icon)");
        $stmt->execute(['name' => $name, 'icon' => $icon]);
        return $this->pdo->lastInsertId();
    }

    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM `categories` WHERE `id` = :id");
        return $stmt->execute(['id' => (int)$id]);
    }
}
