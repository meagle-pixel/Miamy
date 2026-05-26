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

        $horairesAujourdhui = [];
        if (!empty($restaurants)) {
            $ids                = array_map(fn($r) => (int)$r['id_restaurant'], $restaurants);
            $horairesClass      = new Horaires();
            $horairesAujourdhui = $horairesClass->getTodayForRestaurants($ids);
        }

        return compact('restaurants', 'horairesAujourdhui');
    }

    // ---------------------------------------------------------------
    // Ajouter un restaurant
    // ---------------------------------------------------------------
    public function ajouter()
    {
        if (!isset($_SESSION['connected']) || $_SESSION['connected'] !== true || (int)$_SESSION['user']['profil'] !== 2) {
            header('Location: ' . APP_URL . '/connexion');
            exit();
        }

        $message_success = '';
        $message_error   = '';

        $categoryClass = new Category();
        $categories    = $categoryClass->listAll();

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

                // Upload image via le helper centralisé (silencieux en cas d'echec : on garde l'image par defaut)
                $image_name = 'default-resto.jpg';
                if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
                    $uploader = new ImageUploader('restaurants');
                    $new      = $uploader->upload($_FILES['image'], $slug);
                    if ($new) {
                        $image_name = $new;
                    }
                }

                try {
                    $restoClass = new Restaurant();
                    $new_id     = $restoClass->insert([
                        'name'            => $name,
                        'slug'            => $slug,
                        'description'     => $description,
                        'city'            => $city,
                        'main_image'      => $image_name,
                        'id_restaurateur' => $id_restaurateur,
                    ]);

                    if ($new_id && $category_id > 0) {
                        $restoClass->addCategory($new_id, $category_id);
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
            header('Location: ' . APP_URL . '/connexion');
            exit();
        }

        $id_restaurant   = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $id_restaurateur = (int)$_SESSION['user']['profil_id'];

        $restoClass = new Restaurant();
        $resto      = $restoClass->getOwnedBy($id_restaurant, $id_restaurateur);

        if (!$resto) {
            header('Location: ' . APP_URL . '/mon-compte-restaurateur');
            exit();
        }

        $current_category_id = $restoClass->getCurrentCategoryId($id_restaurant);

        $categoryClass   = new Category();
        $categories      = $categoryClass->listAll();
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
                // Upload image via le helper centralisé (on garde l'image existante si pas d'upload ou echec)
                $image_name = $resto['main_image'];
                if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
                    $uploader = new ImageUploader('restaurants');
                    $new      = $uploader->upload($_FILES['image'], $resto['slug'] . '-' . time());
                    if ($new) {
                        $image_name = $new;
                    }
                }

                $ok = $restoClass->updateInfoOwned($id_restaurant, $id_restaurateur, [
                    'name'        => $name,
                    'city'        => $city,
                    'description' => $description,
                    'main_image'  => $image_name,
                ]);

                if ($ok) {
                    // Mise a jour de la categorie via la table de liaison
                    $restoClass->removeCategories($id_restaurant);
                    if ($category_id > 0) {
                        $restoClass->addCategory($id_restaurant, $category_id);
                    }
                    $current_category_id = $category_id;
                    $message_success     = "Restaurant modifié avec succès !";
                    // Re-fetch pour la vue
                    $resto = $restoClass->getById($id_restaurant);
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
            header('Location: ' . APP_URL . '/connexion');
            exit();
        }

        $id_restaurant   = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $id_restaurateur = (int)$_SESSION['user']['profil_id'];

        if (!$id_restaurant) {
            header('Location: ' . APP_URL . '/mon-compte-restaurateur');
            exit();
        }

        $restoClass = new Restaurant();
        $resto      = $restoClass->getOwnedBy($id_restaurant, $id_restaurateur);

        if (!$resto) {
            header('Location: ' . APP_URL . '/mon-compte-restaurateur');
            exit();
        }

        $message_error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_delete'])) {
            try {
                if ($restoClass->delete($id_restaurant)) {
                    header('Location: ' . APP_URL . '/mon-compte-restaurateur?success=deleted');
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

    // ---------------------------------------------------------------
    // Sauvegarde des horaires d'un restaurant (POST depuis details.php)
    // ---------------------------------------------------------------
    public function saveHoraires()
    {
        if (!isset($_SESSION['connected']) || $_SESSION['connected'] !== true || $_SESSION['user']['profil'] > 2) {
            header('Location: ' . APP_URL . '/connexion');
            exit();
        }

        $id_restaurant   = isset($_POST['id_restaurant']) ? (int)$_POST['id_restaurant'] : 0;
        $id_restaurateur = (int)$_SESSION['user']['profil_id'];

        if (!$id_restaurant) {
            header('Location: ' . APP_URL . '/mon-compte-restaurateur');
            exit();
        }

        $restoClass = new Restaurant();
        if (!$restoClass->isOwnedBy($id_restaurant, $id_restaurateur)) {
            header('Location: ' . APP_URL . '/mon-compte-restaurateur');
            exit();
        }

        $data          = $_POST['horaires'] ?? [];
        $horairesClass = new Horaires();
        $ok            = $horairesClass->save($id_restaurant, $data);

        $status = $ok ? 'ok' : 'error';
        header('Location: ' . APP_URL . '/details?id=' . $id_restaurant . '&horaires=' . $status);
        exit();
    }

    // ---------------------------------------------------------------
    // Page details (restaurateur) : stats de la carte + horaires
    // ---------------------------------------------------------------
    public function details()
    {
        if (!isset($_SESSION['connected']) || $_SESSION['connected'] !== true || $_SESSION['user']['profil'] > 2) {
            header('Location: ' . APP_URL . '/connexion');
            exit();
        }

        $id_restaurant   = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $id_restaurateur = (int)$_SESSION['user']['profil_id'];

        if (!$id_restaurant) {
            header('Location: ' . APP_URL . '/mon-compte-restaurateur');
            exit();
        }

        // Verification de propriete
        $restoClass = new Restaurant();
        $resto      = $restoClass->getOwnedBy($id_restaurant, $id_restaurateur);

        if (!$resto) {
            header('Location: ' . APP_URL . '/mon-compte-restaurateur');
            exit();
        }

        // Recuperation des plats + stats
        $platClass = new Plat();
        $plats     = $platClass->getByRestaurant($id_restaurant);

        $totalPlats         = count($plats);
        $platsDisponibles   = count(array_filter($plats, fn($p) => $p['disponible']));
        $platsIndisponibles = $totalPlats - $platsDisponibles;
        $prixMoyen          = $totalPlats > 0
            ? array_sum(array_column($plats, 'prix')) / $totalPlats
            : 0;

        // 3 derniers plats ajoutes
        $dernierPlats = $platClass->getDerniersPlats($id_restaurant);

        // Horaires
        $horairesClass = new Horaires();
        $horaires      = $horairesClass->getByRestaurant($id_restaurant);

        // Messages apres redirection depuis save-horaires
        $horaires_success = isset($_GET['horaires']) && $_GET['horaires'] === 'ok';
        $horaires_error   = isset($_GET['horaires']) && $_GET['horaires'] === 'error';

        return compact(
            'resto', 'id_restaurant',
            'plats', 'totalPlats', 'platsDisponibles', 'platsIndisponibles', 'prixMoyen',
            'dernierPlats',
            'horaires', 'horaires_success', 'horaires_error'
        );
    }
}
