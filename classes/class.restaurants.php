<?php
class Restaurant
{
    private ?PDO $pdo = null;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getConnection();
    }

    public function listRestaurants(bool $activeOnly = true, bool $featuredOnly = false): array
    {
        $query = "SELECT r.*,
                         GROUP_CONCAT(c.name SEPARATOR ', ') AS category_name,
                         MIN(c.id_categorie) AS current_category_id,
                         res.nom    AS proprietaire_nom,
                         res.prenom AS proprietaire_prenom,
                         res.email  AS proprietaire_email
                  FROM `restaurants` r
                  LEFT JOIN `restaurant_categories` rc ON r.id_restaurant = rc.id_restaurant
                  LEFT JOIN `categories` c ON rc.id_categorie = c.id_categorie
                  LEFT JOIN `restaurateurs` res ON res.id = r.id_restaurateur";
        $conditions = [];
        if ($activeOnly)   $conditions[] = "r.subscription_active = 1";
        if ($featuredOnly) $conditions[] = "r.is_featured = 1";

        if (count($conditions) > 0) {
            $query .= " WHERE " . implode(" AND ", $conditions);
        }
        $query .= " GROUP BY r.id_restaurant ORDER BY r.created_at DESC";

        return $this->pdo->query($query)->fetchAll();
    }

    public function getById(int $id): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM `restaurants` WHERE `id_restaurant` = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function getBySlug(string $slug): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM `restaurants` WHERE `slug` = :slug");
        $stmt->execute(['slug' => $slug]);
        return $stmt->fetch() ?: null;
    }

    public function update(int $id, array $data): bool
    {
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
            'id'                  => $id,
        ]);

        // Mise à jour de la catégorie via la table intermédiaire
        if ($result && isset($data['category_id'])) {
            $stmt_del = $this->pdo->prepare("DELETE FROM `restaurant_categories` WHERE `id_restaurant` = :id");
            $stmt_del->execute(['id' => $id]);

            if ((int)$data['category_id'] > 0) {
                $stmt_cat = $this->pdo->prepare(
                    "INSERT INTO `restaurant_categories` (id_restaurant, id_categorie) VALUES (:id_restaurant, :id_categorie)"
                );
                $stmt_cat->execute([
                    'id_restaurant' => $id,
                    'id_categorie'  => (int)$data['category_id'],
                ]);
            }
        }

        return $result;
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM `restaurants` WHERE `id_restaurant` = :id");
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Met à jour la catégorie d'un restaurant (vue admin).
     *
     * On efface les liens existants dans `restaurant_categories` pour ce restaurant,
     * puis on insère le nouveau lien si une catégorie a été choisie. Si $idCategorie
     * vaut 0, le restaurant se retrouve sans catégorie (cas "— Aucune —").
     *
     * Le tout est encapsulé dans une transaction pour garantir un état cohérent
     * même en cas d'erreur entre le DELETE et l'INSERT.
     */
    public function updateCategory(int $idRestaurant, int $idCategorie): bool
    {
        $this->pdo->beginTransaction();
        try {
            // 1. Vider les liens existants pour ce restaurant
            $stmt_del = $this->pdo->prepare(
                "DELETE FROM `restaurant_categories` WHERE `id_restaurant` = :id"
            );
            $stmt_del->execute(['id' => $idRestaurant]);

            // 2. Insérer la nouvelle catégorie (si l'admin n'a pas choisi "Aucune")
            if ($idCategorie > 0) {
                $stmt_ins = $this->pdo->prepare(
                    "INSERT INTO `restaurant_categories` (id_restaurant, id_categorie)
                     VALUES (:id_restaurant, :id_categorie)"
                );
                $stmt_ins->execute([
                    'id_restaurant' => $idRestaurant,
                    'id_categorie'  => $idCategorie,
                ]);
            }

            $this->pdo->commit();
            return true;

        } catch (PDOException $e) {
            $this->pdo->rollBack();
            throw $e; // on relance pour que le caller affiche le message
        }
    }
}
