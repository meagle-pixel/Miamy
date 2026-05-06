<?php

class PlatController
{
    // ---------------------------------------------------------------
    // Liste des plats (publique — vue déjà pure HTML, rien à préparer)
    // ---------------------------------------------------------------
    public function liste()
    {
        $id_restaurant = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        if ($id_restaurant === 0) {
            header('Location: ' . $GLOBALS['url'] . '/liste-restaurants');
            exit();
        }

        $restoClass = new Restaurant();
        $resto = $restoClass->getById($id_restaurant);

        if (!$resto) {
            header('Location: ' . $GLOBALS['url'] . '/liste-restaurants');
            exit();
        }

        $platClass = new Plat();
        $plats     = $platClass->getByRestaurant($id_restaurant);

        return compact('resto', 'plats', 'id_restaurant');
    }

    // ---------------------------------------------------------------
    // Gestion de la carte (restaurateur)
    // ---------------------------------------------------------------
    public function gestionCarte()
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
        $stmt = $pdo->prepare("SELECT * FROM restaurants WHERE id_restaurant = :id AND id_restaurateur = :id_proprio");
        $stmt->execute([
            'id'         => $id_restaurant,
            'id_proprio' => $id_restaurateur,
        ]);
        $resto = $stmt->fetch();

        if (!$resto) {
            header('Location: ' . $GLOBALS['url'] . '/mon-compte-restaurateur');
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

    // ---------------------------------------------------------------
    // Ajouter un plat
    // ---------------------------------------------------------------
    public function ajouter()
    {
        if (!isset($_SESSION['connected']) || $_SESSION['connected'] !== true || $_SESSION['user']['profil'] > 2) {
            header('Location: ' . $GLOBALS['url'] . '/connexion');
            exit();
        }

        $id_restaurant   = isset($_GET['id_restaurant']) ? (int)$_GET['id_restaurant'] : 0;
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
                $image_name = null;
                if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
                    $allowed = ['jpg', 'jpeg', 'png', 'webp'];
                    $ext     = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));

                    if (in_array($ext, $allowed) && $_FILES['image']['size'] < 5000000) {
                        $slug_plat   = strtolower(preg_replace('/[^A-Za-z0-9-]+/', '-', $nom));
                        $image_name  = $slug_plat . '-' . time() . '.' . $ext;
                        $upload_dir  = $GLOBALS['dev']
                            ? $_SERVER['DOCUMENT_ROOT'] . '/Miamy/assets/img/plats/'
                            : $_SERVER['DOCUMENT_ROOT'] . '/assets/img/plats/';
                        $upload_path = $upload_dir . $image_name;

                        if (!is_dir($upload_dir)) {
                            mkdir($upload_dir, 0755, true);
                        }

                        if (!move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                            $image_name    = null;
                            $message_error = "Erreur lors de l'upload de l'image.";
                        }
                    } else {
                        $message_error = "Image invalide (formats acceptés : JPG, PNG, WebP — max 5 Mo).";
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
                        header('Location: ' . $GLOBALS['url'] . '/gestion-carte?id=' . $id_restaurant . '&success=added');
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

    // ---------------------------------------------------------------
    // Modifier un plat
    // ---------------------------------------------------------------
    public function modifier()
    {
        if (!isset($_SESSION['connected']) || $_SESSION['connected'] !== true || $_SESSION['user']['profil'] > 2) {
            header('Location: ' . $GLOBALS['url'] . '/connexion');
            exit();
        }

        $id_plat         = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $id_restaurateur = $_SESSION['user']['profil_id'];

        if (!$id_plat) {
            header('Location: ' . $GLOBALS['url'] . '/mon-compte-restaurateur');
            exit();
        }

        $platClass = new Plat();
        $plat      = $platClass->getById($id_plat);

        if (!$plat) {
            header('Location: ' . $GLOBALS['url'] . '/mon-compte-restaurateur');
            exit();
        }

        $id_restaurant = $plat['id_restaurant'];

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

            $image_name = $plat['image'];

            if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
                $allowed = ['jpg', 'jpeg', 'png', 'webp'];
                $ext     = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));

                if (!in_array($ext, $allowed)) {
                    $errors[] = "Format d'image non supporté (JPG, PNG, WebP uniquement).";
                } elseif ($_FILES['image']['size'] > 5000000) {
                    $errors[] = "L'image est trop volumineuse (maximum 5 Mo).";
                } else {
                    $slug_plat   = strtolower(preg_replace('/[^A-Za-z0-9-]+/', '-', $nom));
                    $new_image   = $slug_plat . '-' . time() . '.' . $ext;
                    $upload_dir  = $GLOBALS['dev'] ? '/Miamy/assets/img/plats/' : '/assets/img/plats/';
                    $upload_path = $_SERVER['DOCUMENT_ROOT'] . $upload_dir . $new_image;

                    if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                        $image_name = $new_image;
                    } else {
                        $errors[] = "Erreur technique lors du téléchargement de l'image.";
                    }
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
                    header('Location: ' . $GLOBALS['url'] . '/gestion-carte?id=' . $id_restaurant . '&success=updated');
                    exit();
                } else {
                    $errors[] = "Une erreur est survenue lors de la mise à jour en base de données.";
                }
            }
        }

        return compact('resto', 'plat', 'id_plat', 'id_restaurant', 'categoriesSuggestions', 'errors');
    }

    // ---------------------------------------------------------------
    // Supprimer un plat
    // ---------------------------------------------------------------
    public function supprimer()
    {
        if (!isset($_SESSION['connected']) || $_SESSION['connected'] !== true || $_SESSION['user']['profil'] > 2) {
            header('Location: ' . $GLOBALS['url'] . '/connexion');
            exit();
        }

        $id_plat         = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $id_restaurant   = isset($_GET['id_restaurant']) ? (int)$_GET['id_restaurant'] : 0;
        $id_restaurateur = $_SESSION['user']['profil_id'];

        if (!$id_plat || !$id_restaurant) {
            header('Location: ' . $GLOBALS['url'] . '/mon-compte-restaurateur');
            exit();
        }

        $platClass = new Plat();
        $plat      = $platClass->getById($id_plat);

        if (!$plat || $plat['id_restaurant'] !== $id_restaurant) {
            header('Location: ' . $GLOBALS['url'] . '/mon-compte-restaurateur');
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
            header('Location: ' . $GLOBALS['url'] . '/mon-compte-restaurateur');
            exit();
        }

        $message_error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_delete'])) {
            if ($platClass->delete($id_plat)) {
                header('Location: ' . $GLOBALS['url'] . '/gestion-carte?id=' . $id_restaurant . '&success=deleted');
                exit();
            } else {
                $message_error = "Une erreur est survenue lors de la suppression.";
            }
        }

        return compact('resto', 'plat', 'id_plat', 'id_restaurant', 'message_error');
    }
}
