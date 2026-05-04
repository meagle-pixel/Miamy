<?php

class RestaurantController
{
    // ---------------------------------------------------------------
    // Liste des restaurants (publique)
    // ---------------------------------------------------------------
    public function liste()
    {
        $restoClass  = new Restaurant();
        $restaurants = $restoClass->listRestaurants(activeOnly: false);

        $jourAujourdhui     = (int)date('N') - 1;
        $pdo                = Database::getInstance()->getConnection();
        $horairesAujourdhui = [];

        if (!empty($restaurants)) {
            $ids  = implode(',', array_map(fn($r) => (int)$r['id_restaurant'], $restaurants));
            $stmt = $pdo->query(
                "SELECT * FROM horaires WHERE id_restaurant IN ($ids) AND jour = $jourAujourdhui"
            );
            foreach ($stmt->fetchAll() as $h) {
                $horairesAujourdhui[(int)$h['id_restaurant']] = $h;
            }
        }

        return compact('restaurants', 'horairesAujourdhui');
    }

    // ---------------------------------------------------------------
    // Ajouter un restaurant
    // ---------------------------------------------------------------
    public function ajouter()
    {
        if (!isset($_SESSION['connected']) || $_SESSION['connected'] !== true || (int)$_SESSION['user']['profil'] !== 2) {
            header('Location: ' . $GLOBALS['url'] . '/connexion');
            exit();
        }

        $message_success = '';
        $message_error   = '';

        $pdo        = Database::getInstance()->getConnection();
        $categories = $pdo->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll();

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_restaurant'])) {

            $name            = trim($_POST['name'] ?? '');
            $city            = trim($_POST['city'] ?? '');
            $description     = trim($_POST['description'] ?? '');
            $category_id     = (int)($_POST['category_id'] ?? 0);
            $id_restaurateur = (int)($_SESSION['user']['profil_id'] ?? 0);

            if ($id_restaurateur <= 0) {
                $message_error = "Compte restaurateur introuvable. Veuillez contacter l'administrateur.";
            } elseif (empty($name) || empty($city)) {
                $message_error = "Le nom et la ville sont obligatoires.";
            } else {
                $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
                $slug = $slug . '-' . uniqid();

                $image_name = 'default-resto.jpg';
                if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
                    $allowed = ['jpg', 'jpeg', 'png', 'webp'];
                    $ext     = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));

                    if (in_array($ext, $allowed) && $_FILES['image']['size'] < 5000000) {
                        $image_name  = $slug . '.' . $ext;
                        $upload_path = $GLOBALS['dev']
                            ? $_SERVER['DOCUMENT_ROOT'] . '/Miamy/assets/img/restaurants/' . $image_name
                            : $_SERVER['DOCUMENT_ROOT'] . '/assets/img/restaurants/' . $image_name;

                        move_uploaded_file($_FILES['image']['tmp_name'], $upload_path);
                    }
                }

                try {
                    $stmt = $pdo->prepare(
                        "INSERT INTO restaurants (name, slug, description, city, main_image, id_restaurateur, created_at)
                         VALUES (:name, :slug, :description, :city, :main_image, :id_restaurateur, NOW())"
                    );
                    $stmt->execute([
                        'name'            => $name,
                        'slug'            => $slug,
                        'description'     => $description,
                        'city'            => $city,
                        'main_image'      => $image_name,
                        'id_restaurateur' => $id_restaurateur,
                    ]);

                    $new_id = $pdo->lastInsertId();

                    if ($category_id > 0) {
                        $stmt_cat = $pdo->prepare(
                            "INSERT INTO restaurant_categories (id_restaurant, id_categorie) VALUES (:id_restaurant, :id_categorie)"
                        );
                        $stmt_cat->execute([
                            'id_restaurant' => $new_id,
                            'id_categorie'  => $category_id,
                        ]);
                    }

                    $message_success = "Restaurant ajouté avec succès !";

                } catch (PDOException $e) {
                    error_log('[ajouter-restaurant] ' . $e->getMessage());
                    $message_error = "Erreur lors de l'ajout du restaurant. Détail technique : " . $e->getMessage();
                }
            }
        }

        return compact('categories', 'message_success', 'message_error');
    }

    // ---------------------------------------------------------------
    // Modifier un restaurant
    // ---------------------------------------------------------------
    public function modifier()
    {
        if (!isset($_SESSION['connected']) || $_SESSION['connected'] !== true || $_SESSION['user']['profil'] > 2) {
            header('Location: ' . $GLOBALS['url'] . '/connexion');
            exit();
        }

        $id_restaurant   = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $id_restaurateur = $_SESSION['user']['profil_id'];
        $pdo             = Database::getInstance()->getConnection();

        $stmt = $pdo->prepare("SELECT * FROM restaurants WHERE id_restaurant = :id AND id_restaurateur = :id_restaurateur");
        $stmt->execute([
            'id'              => $id_restaurant,
            'id_restaurateur' => $id_restaurateur,
        ]);
        $resto = $stmt->fetch();

        if (!$resto) {
            header('Location: ' . $GLOBALS['url'] . '/mon-compte-restaurateur');
            exit();
        }

        $stmt_cur_cat = $pdo->prepare("SELECT id_categorie FROM restaurant_categories WHERE id_restaurant = :id LIMIT 1");
        $stmt_cur_cat->execute(['id' => $id_restaurant]);
        $current_category_id = (int)($stmt_cur_cat->fetchColumn() ?: 0);

        $categories      = $pdo->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll();
        $message_success = '';
        $message_error   = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_update'])) {

            $name        = trim($_POST['name'] ?? '');
            $city        = trim($_POST['city'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $category_id = (int)($_POST['category_id'] ?? 0);

            if (empty($name) || empty($city)) {
                $message_error = "Le nom et la ville sont obligatoires.";
            } else {
                $image_name = $resto['main_image'];

                if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
                    $allowed = ['jpg', 'jpeg', 'png', 'webp'];
                    $ext     = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));

                    if (in_array($ext, $allowed) && $_FILES['image']['size'] < 5000000) {
                        $image_name  = $resto['slug'] . '-' . time() . '.' . $ext;
                        $upload_path = $GLOBALS['dev']
                            ? $_SERVER['DOCUMENT_ROOT'] . '/Miamy/assets/img/restaurants/' . $image_name
                            : $_SERVER['DOCUMENT_ROOT'] . '/assets/img/restaurants/' . $image_name;

                        move_uploaded_file($_FILES['image']['tmp_name'], $upload_path);
                    }
                }

                $upd = $pdo->prepare(
                    "UPDATE restaurants SET
                        name = :name, city = :city, description = :description, main_image = :main_image
                     WHERE id_restaurant = :id AND id_restaurateur = :id_restaurateur"
                );

                if ($upd->execute([
                    'name'            => $name,
                    'city'            => $city,
                    'description'     => $description,
                    'main_image'      => $image_name,
                    'id'              => $id_restaurant,
                    'id_restaurateur' => $id_restaurateur,
                ])) {
                    $pdo->prepare("DELETE FROM restaurant_categories WHERE id_restaurant = :id")->execute(['id' => $id_restaurant]);
                    if ($category_id > 0) {
                        $pdo->prepare("INSERT INTO restaurant_categories (id_restaurant, id_categorie) VALUES (:id_restaurant, :id_categorie)")
                            ->execute(['id_restaurant' => $id_restaurant, 'id_categorie' => $category_id]);
                    }
                    $current_category_id = $category_id;
                    $message_success     = "Restaurant modifié avec succès !";
                    $stmt2               = $pdo->prepare("SELECT * FROM restaurants WHERE id_restaurant = :id");
                    $stmt2->execute(['id' => $id_restaurant]);
                    $resto = $stmt2->fetch();
                } else {
                    $message_error = "Erreur lors de la modification.";
                }
            }
        }

        return compact('resto', 'id_restaurant', 'categories', 'current_category_id', 'message_success', 'message_error');
    }

    // ---------------------------------------------------------------
    // Supprimer un restaurant
    // ---------------------------------------------------------------
    public function supprimer()
    {
        if (!isset($_SESSION['connected']) || $_SESSION['connected'] !== true || $_SESSION['user']['profil'] > 2) {
            header('Location: ' . $GLOBALS['url'] . '/connexion');
            exit();
        }

        $id_restaurant   = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $id_restaurateur = $_SESSION['user']['profil_id'];

        if (!$id_restaurant) {
            header('Location: ' . $GLOBALS['url'] . '/mon-compte-restaurateur');
            exit();
        }

        $pdo  = Database::getInstance()->getConnection();
        $stmt = $pdo->prepare("SELECT * FROM restaurants WHERE id_restaurant = :id AND id_restaurateur = :id_restaurateur");
        $stmt->execute([
            'id'              => $id_restaurant,
            'id_restaurateur' => $id_restaurateur,
        ]);
        $resto = $stmt->fetch();

        if (!$resto) {
            header('Location: ' . $GLOBALS['url'] . '/mon-compte-restaurateur');
            exit();
        }

        $message_error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_delete'])) {
            try {
                $restaurant = new Restaurant();
                if ($restaurant->delete($id_restaurant)) {
                    header('Location: ' . $GLOBALS['url'] . '/mon-compte-restaurateur?success=deleted');
                    exit();
                } else {
                    $message_error = "Une erreur est survenue lors de la suppression.";
                }
            } catch (PDOException $e) {
                error_log('[supprimer-restaurant] ' . $e->getMessage());
                $message_error = "Erreur lors de la suppression. Détail technique : " . $e->getMessage();
            }
        }

        return compact('resto', 'id_restaurant', 'message_error');
    }
}
