<?php

class PlatController
{
    // Liste des plats 
    public function liste()
    {
        $id_restaurant = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        if ($id_restaurant === 0) {
            header('Location: ' . APP_URL . '/liste-restaurants');
            exit();
        }

        $restoClass = new Restaurant();
        $resto = $restoClass->getById($id_restaurant);

        if (!$resto) {
            header('Location: ' . APP_URL . '/liste-restaurants');
            exit();
        }

        $platClass = new Plat();
        $allPlats  = $platClass->getByRestaurant($id_restaurant);

        // Cote client : on ne montre que les plats actuellement disponibles
        $plats = array_values(array_filter($allPlats, fn($p) => (int)$p['disponible'] === 1));

        // Groupement par categorie pour faciliter l'affichage de sections
        $platsParCategorie = [];
        foreach ($plats as $plat) {
            $platsParCategorie[$plat['categorie']][] = $plat;
        }

        return compact('resto', 'plats', 'platsParCategorie', 'id_restaurant');
    }

    
    // Gestion de la carte (restaurateur)
    
    public function gestionCarte()
    {
        if (!isset($_SESSION['connected']) || $_SESSION['connected'] !== true || $_SESSION['user']['profil'] > 2) {
            header('Location: ' . APP_URL . '/connexion');
            exit();
        }

        $id_restaurant   = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $id_restaurateur = $_SESSION['user']['profil_id'];

        if (!$id_restaurant) {
            header('Location: ' . APP_URL . '/mon-compte-restaurateur');
            exit();
        }

        $pdo  = Database::getInstance()->getConnection();
        $stmt = $pdo->prepare("SELECT * FROM restaurants WHERE id_restaurant = :id AND id_restaurateur = :id_proprio");
        $stmt->execute([
            'id'         => $id_restaurant,
            'id_proprio' => $id_restaurateur,
        ]);
        $resto = $stmt->fetch();

        if (!$resto) {
            header('Location: ' . APP_URL . '/mon-compte-restaurateur');
            exit();
        }

        $platClass = new Plat();
        $plats     = $platClass->getByRestaurant($id_restaurant);

        $platsParCategorie = [];
        foreach ($plats as $plat) {
            $platsParCategorie[$plat['categorie']][] = $plat;
        }

        $message_success = '';
        $message_error   = '';
        if (isset($_GET['success'])) {
            $msgs = [
                'added'   => 'Plat ajouté avec succès !',
                'updated' => 'Plat modifié avec succès !',
                'deleted' => 'Plat supprimé avec succès.',
            ];
            $message_success = $msgs[$_GET['success']] ?? '';
        }

        return compact('resto', 'plats', 'platsParCategorie', 'message_success', 'message_error', 'id_restaurant');
    }

    
    // Ajouter un plat
    
    public function ajouter()
    {
        if (!isset($_SESSION['connected']) || $_SESSION['connected'] !== true || $_SESSION['user']['profil'] > 2) {
            header('Location: ' . APP_URL . '/connexion');
            exit();
        }

        $id_restaurant   = isset($_GET['id_restaurant']) ? (int)$_GET['id_restaurant'] : 0;
        $id_restaurateur = $_SESSION['user']['profil_id'];

        if (!$id_restaurant) {
            header('Location: ' . APP_URL . '/mon-compte-restaurateur');
            exit();
        }

        $restoClass = new Restaurant();
        $resto      = $restoClass->getOwnedBy($id_restaurant, (int)$id_restaurateur);

        if (!$resto) {
            header('Location: ' . APP_URL . '/mon-compte-restaurateur');
            exit();
        }

        $platClass            = new Plat();
        $categoriesExistantes = $platClass->getCategoriesByRestaurant($id_restaurant);
        $message_success      = '';
        $message_error        = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_plat'])) {

            $nom         = trim($_POST['nom'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $prix        = str_replace(',', '.', trim($_POST['prix'] ?? '0'));
            $categorie   = trim($_POST['categorie'] ?? 'Plats');
            $disponible  = isset($_POST['disponible']) ? 1 : 0;

            if (empty($nom)) {
                $message_error = "Le nom du plat est obligatoire.";
            } elseif (!is_numeric($prix) || $prix < 0) {
                $message_error = "Le prix doit être un nombre valide.";
            } else {
                // Upload image via le helper centralisé
                $image_name = null;
                if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
                    $uploader   = new ImageUploader('plats');
                    $slug_plat  = strtolower(preg_replace('/[^A-Za-z0-9-]+/', '-', $nom));
                    $image_name = $uploader->upload($_FILES['image'], $slug_plat . '-' . time());
                    if ($uploader->error) {
                        $message_error = $uploader->error;
                    }
                }

                if (empty($message_error)) {
                    $data = [
                        'nom'           => $nom,
                        'description'   => $description,
                        'prix'          => (float)$prix,
                        'image'         => $image_name,
                        'categorie'     => $categorie,
                        'id_restaurant' => $id_restaurant,
                        'disponible'    => $disponible,
                    ];

                    if ($platClass->insert($data)) {
                        header('Location: ' . APP_URL . '/gestion-carte?id=' . $id_restaurant . '&success=added');
                        exit();
                    } else {
                        $message_error = "Erreur lors de l'ajout du plat.";
                    }
                }
            }
        }

        $categoriesBase        = ['Entrées', 'Plats', 'Desserts', 'Boissons', 'Snacks'];
        $categoriesSuggestions = array_values(array_unique(array_merge(
            $categoriesBase,
            array_filter($categoriesExistantes, fn($v) => is_string($v) && $v !== '')
        )));
        sort($categoriesSuggestions);

        return compact('resto', 'id_restaurant', 'categoriesSuggestions', 'message_success', 'message_error');
    }

    
    // Modifier un plat
    
    public function modifier()
    {
        if (!isset($_SESSION['connected']) || $_SESSION['connected'] !== true || $_SESSION['user']['profil'] > 2) {
            header('Location: ' . APP_URL . '/connexion');
            exit();
        }

        $id_plat         = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $id_restaurateur = $_SESSION['user']['profil_id'];

        if (!$id_plat) {
            header('Location: ' . APP_URL . '/mon-compte-restaurateur');
            exit();
        }

        $platClass = new Plat();
        $plat      = $platClass->getById($id_plat);

        if (!$plat) {
            header('Location: ' . APP_URL . '/mon-compte-restaurateur');
            exit();
        }

        $id_restaurant = $plat['id_restaurant'];

        $restoClass = new Restaurant();
        $resto      = $restoClass->getOwnedBy((int)$id_restaurant, (int)$id_restaurateur);

        if (!$resto) {
            header('Location: ' . APP_URL . '/mon-compte-restaurateur');
            exit();
        }

        $categoriesExistantes  = $platClass->getCategoriesByRestaurant($id_restaurant);
        $categoriesBase        = ['Entrées', 'Plats', 'Desserts', 'Boissons', 'Snacks'];
        $categoriesSuggestions = array_values(array_unique(array_merge(
            $categoriesBase,
            array_filter($categoriesExistantes, fn($v) => is_string($v) && $v !== '')
        )));

        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_update'])) {

            $nom         = trim($_POST['nom'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $prix        = str_replace(',', '.', trim($_POST['prix'] ?? '0'));
            $categorie   = trim($_POST['categorie'] ?? 'Plats');
            $disponible  = isset($_POST['disponible']) ? 1 : 0;

            if (empty($nom))                         $errors[] = "Le nom du plat est obligatoire.";
            if (!is_numeric($prix) || $prix < 0)     $errors[] = "Le prix doit être un nombre valide et positif.";

            // Upload image via le helper centralisé (on garde l'image existante si pas d'upload)
            $image_name = $plat['image'];
            if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
                $uploader  = new ImageUploader('plats');
                $slug_plat = strtolower(preg_replace('/[^A-Za-z0-9-]+/', '-', $nom));
                $new_image = $uploader->upload($_FILES['image'], $slug_plat . '-' . time());
                if ($new_image) {
                    $image_name = $new_image;
                } elseif ($uploader->error) {
                    $errors[] = $uploader->error;
                }
            }

            if (empty($errors)) {
                $data = [
                    'nom'         => $nom,
                    'description' => $description,
                    'prix'        => (float)$prix,
                    'image'       => $image_name,
                    'categorie'   => $categorie,
                    'disponible'  => $disponible,
                ];

                if ($platClass->update($id_plat, $data)) {
                    header('Location: ' . APP_URL . '/gestion-carte?id=' . $id_restaurant . '&success=updated');
                    exit();
                } else {
                    $errors[] = "Une erreur est survenue lors de la mise à jour en base de données.";
                }
            }
        }

        return compact('resto', 'plat', 'id_plat', 'id_restaurant', 'categoriesSuggestions', 'errors');
    }
    public function supprimer()
    {
        if (!isset($_SESSION['connected']) || $_SESSION['connected'] !== true || $_SESSION['user']['profil'] > 2) {
            header('Location: ' . APP_URL . '/connexion');
            exit();
        }

        $id_plat         = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $id_restaurant   = isset($_GET['id_restaurant']) ? (int)$_GET['id_restaurant'] : 0;
        $id_restaurateur = $_SESSION['user']['profil_id'];

        if (!$id_plat || !$id_restaurant) {
            header('Location: ' . APP_URL . '/mon-compte-restaurateur');
            exit();
        }

        $platClass = new Plat();
        $plat      = $platClass->getById($id_plat);

        if (!$plat || $plat['id_restaurant'] !== $id_restaurant) {
            header('Location: ' . APP_URL . '/mon-compte-restaurateur');
            exit();
        }

        $pdo  = Database::getInstance()->getConnection();
        $stmt = $pdo->prepare("SELECT name FROM restaurants WHERE id_restaurant = :id AND id_restaurateur = :id_restaurateur");
        $stmt->execute([
            'id'              => $id_restaurant,
            'id_restaurateur' => $id_restaurateur,
        ]);
        $resto = $stmt->fetch();

        if (!$resto) {
            header('Location: ' . APP_URL . '/mon-compte-restaurateur');
            exit();
        }

        $message_error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_delete'])) {
            if ($platClass->delete($id_plat)) {
                header('Location: ' . APP_URL . '/gestion-carte?id=' . $id_restaurant . '&success=deleted');
                exit();
            } else {
                $message_error = "Une erreur est survenue lors de la suppression.";
            }
        }

        return compact('resto', 'plat', 'id_plat', 'id_restaurant', 'message_error');
    }

    // ---------------------------------------------------------------
    // AJAX : bascule la disponibilite d'un plat (returns JSON)
    // ---------------------------------------------------------------
    public function toggleDisponible()
    {
        header('Content-Type: application/json');

        if (!isset($_SESSION['connected']) || $_SESSION['connected'] !== true || $_SESSION['user']['profil'] > 2) {
            echo json_encode(['success' => false, 'message' => 'Non autorise']);
            exit();
        }

        $id_plat         = isset($_POST['id_plat']) ? (int)$_POST['id_plat'] : 0;
        $id_restaurateur = (int)$_SESSION['user']['profil_id'];

        if (!$id_plat) {
            echo json_encode(['success' => false, 'message' => 'ID invalide']);
            exit();
        }

        $platClass = new Plat();
        $plat      = $platClass->getOwnedBy($id_plat, $id_restaurateur);

        if (!$plat) {
            echo json_encode(['success' => false, 'message' => 'Plat introuvable ou acces refuse']);
            exit();
        }

        $ok         = $platClass->toggleDisponible($id_plat);
        $nouvelEtat = $plat['disponible'] ? 0 : 1;

        echo json_encode(['success' => (bool)$ok, 'disponible' => $nouvelEtat]);
        exit();
    }

    // ---------------------------------------------------------------
    // AJAX : changer la categorie d'un plat (returns JSON)
    // ---------------------------------------------------------------
    public function updateCategorie()
    {
        header('Content-Type: application/json');

        if (!isset($_SESSION['connected']) || $_SESSION['connected'] !== true || $_SESSION['user']['profil'] > 2) {
            echo json_encode(['success' => false, 'message' => 'Non autorise']);
            exit();
        }

        $id_plat         = isset($_POST['id_plat'])   ? (int)trim($_POST['id_plat'])   : 0;
        $nouvelle_cat    = isset($_POST['categorie']) ? trim($_POST['categorie'])       : '';
        $id_restaurateur = (int)$_SESSION['user']['profil_id'];

        $categories_valides = ['Entrées', 'Plats', 'Desserts', 'Boissons', 'Snacks'];

        if (!$id_plat || !in_array($nouvelle_cat, $categories_valides, true)) {
            echo json_encode(['success' => false, 'message' => 'Donnees invalides']);
            exit();
        }

        $platClass = new Plat();

        if (!$platClass->isOwnedBy($id_plat, $id_restaurateur)) {
            echo json_encode(['success' => false, 'message' => 'Plat introuvable ou acces refuse']);
            exit();
        }

        $ok = $platClass->updateCategorie($id_plat, $nouvelle_cat);
        echo json_encode(['success' => (bool)$ok]);
        exit();
    }
}
