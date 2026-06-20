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

    /*
      Met à jour la catégorie d'un restaurant (vue admin).
     
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

    /**
     * Retourne le restaurant si l'utilisateur donne en est bien proprietaire,
     * sinon null. Utilise pour la verification d'autorisation dans les
     * controllers (ownership check).
     */
    public function getOwnedBy(int $id, int $idOwner): ?array
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM `restaurants` WHERE `id_restaurant` = :id AND `id_restaurateur` = :id_owner"
        );
        $stmt->execute(['id' => $id, 'id_owner' => $idOwner]);
        return $stmt->fetch() ?: null;
    }

    /**
     * Cree un nouveau restaurant. Retourne l'ID insere, ou false en cas d'echec.
     * Le slug doit etre fourni par l'appelant (cf RestaurantController::ajouter).
     */
    public function insert(array $data)
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO `restaurants` (name, slug, description, city, main_image, id_restaurateur, created_at)
             VALUES (:name, :slug, :description, :city, :main_image, :id_restaurateur, NOW())"
        );
        $ok = $stmt->execute([
            'name'            => $data['name'],
            'slug'            => $data['slug'],
            'description'     => $data['description'],
            'city'            => $data['city'],
            'main_image'      => $data['main_image'],
            'id_restaurateur' => (int)$data['id_restaurateur'],
        ]);
        return $ok ? (int)$this->pdo->lastInsertId() : false;
    }

    /**
     * Associe un restaurant a une categorie (table de liaison).
     */
    public function addCategory(int $idRestaurant, int $idCategorie): bool
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO `restaurant_categories` (id_restaurant, id_categorie) VALUES (:id_restaurant, :id_categorie)"
        );
        return $stmt->execute([
            'id_restaurant' => $idRestaurant,
            'id_categorie'  => $idCategorie,
        ]);
    }

    /**
     * Vide tous les liens categorie d'un restaurant.
     */
    public function removeCategories(int $idRestaurant): bool
    {
        $stmt = $this->pdo->prepare(
            "DELETE FROM `restaurant_categories` WHERE `id_restaurant` = :id"
        );
        return $stmt->execute(['id' => $idRestaurant]);
    }

    /**
     * Retourne la categorie courante d'un restaurant (0 si aucune).
     */
    public function getCurrentCategoryId(int $idRestaurant): int
    {
        $stmt = $this->pdo->prepare(
            "SELECT id_categorie FROM `restaurant_categories` WHERE `id_restaurant` = :id LIMIT 1"
        );
        $stmt->execute(['id' => $idRestaurant]);
        return (int)($stmt->fetchColumn() ?: 0);
    }

    /**
     * Verifie qu'un restaurant appartient bien a un proprietaire donne.
     * Version booleenne (plus rapide quand on n'a pas besoin des donnees).
     */
    public function isOwnedBy(int $id, int $idOwner): bool
    {
        $stmt = $this->pdo->prepare(
            "SELECT 1 FROM `restaurants` WHERE `id_restaurant` = :id AND `id_restaurateur` = :id_owner LIMIT 1"
        );
        $stmt->execute(['id' => $id, 'id_owner' => $idOwner]);
        return (bool)$stmt->fetchColumn();
    }

    /**
     * Met a jour les infos basiques d'un restaurant (nom, ville, description, image),
     * a condition que le restaurant appartienne bien au proprietaire donne.
     * Differe de update() qui modifie aussi is_featured/subscription_active (utilise par l'admin).
     */
    public function updateInfoOwned(int $id, int $idOwner, array $data): bool
    {
        $stmt = $this->pdo->prepare(
            "UPDATE `restaurants` SET
                `name`        = :name,
                `city`        = :city,
                `description` = :description,
                `main_image`  = :main_image
             WHERE `id_restaurant` = :id AND `id_restaurateur` = :id_owner"
        );
        return $stmt->execute([
            'name'        => $data['name'],
            'city'        => $data['city'],
            'description' => $data['description'],
            'main_image'  => $data['main_image'],
            'id'          => $id,
            'id_owner'    => $idOwner,
        ]);
    }

    /**
     * Retourne tous les restaurants appartenant a un proprietaire,
     * tries par nom alphabetique.
     */
    public function listByOwner(int $idOwner): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM `restaurants` WHERE `id_restaurateur` = :id_owner ORDER BY `name` ASC"
        );
        $stmt->execute(['id_owner' => $idOwner]);
        return $stmt->fetchAll();
    }
}
