<?php

/**
 * AdminController : pages d'administration (profil = 1).
 *
 * Methodes :
 *   - dashboard()       : statistiques globales + listes recentes
 *   - panel()           : gestion des utilisateurs (clients + restaurateurs)
 *   - restaurants()     : gestion des restaurants (suppression + categorie)
 *   - ajouterAdmin()    : creation d'un nouveau compte admin
 *
 * Toutes les methodes verifient en entree que l'utilisateur est connecte
 * en tant qu'admin (profil <= 1). Sinon redirection vers /connexion.
 */
class AdminController
{
    // ---------------------------------------------------------------
    // Tableau de bord admin
    // ---------------------------------------------------------------
    public function dashboard(): array
    {
        Auth::requireAdmin();

        $pdo = Database::getInstance()->getConnection();

        // Compteurs globaux. Chaque requete est isolee dans son try/catch
        // pour que le dashboard reste lisible meme si une table est cassee.
        $nb_restaurants_total  = 0;
        $nb_restaurants_actifs = 0;
        $nb_utilisateurs       = 0;
        $nb_plats              = 0;
        $nb_commandes_jour     = 0;
        $ca_jour               = 0;
        $nb_promos_actives     = 0;
        $derniers_utilisateurs = [];
        $derniers_restaurants  = [];
        $derniers_logs         = [];

        try {
            $nb_restaurants_total = (int) $pdo->query("SELECT COUNT(*) FROM restaurants")->fetchColumn();
        } catch (Exception $e) {
            error_log('[dashboard] nb_restaurants_total : ' . $e->getMessage());
        }

        try {
            $nb_restaurants_actifs = (int) $pdo->query("SELECT COUNT(*) FROM restaurants WHERE subscription_active = 1")->fetchColumn();
        } catch (Exception $e) {
            error_log('[dashboard] nb_restaurants_actifs : ' . $e->getMessage());
        }

        try {
            $nb_utilisateurs = (int) $pdo->query("SELECT COUNT(*) FROM utilisateurs WHERE profil IN (2, 3)")->fetchColumn();
        } catch (Exception $e) {
            error_log('[dashboard] nb_utilisateurs : ' . $e->getMessage());
        }

        try {
            $nb_plats = (int) $pdo->query("SELECT COUNT(*) FROM plats WHERE disponible = 1")->fetchColumn();
        } catch (Exception $e) {
            error_log('[dashboard] nb_plats : ' . $e->getMessage());
        }

        try {
            $stmt = $pdo->query("SELECT COUNT(*), COALESCE(SUM(totalttc), 0) FROM commandes WHERE DATE(date_commande) = CURDATE()");
            $row  = $stmt->fetch(PDO::FETCH_NUM);
            $nb_commandes_jour = (int) $row[0];
            $ca_jour           = (float) $row[1];
        } catch (Exception $e) {
            error_log('[dashboard] commandes_jour : ' . $e->getMessage());
        }

        try {
            $stmt = $pdo->query("
                SELECT u.email, u.dateinscription,
                       CASE u.profil WHEN 2 THEN 'Restaurateur' ELSE 'Client' END AS role
                FROM utilisateurs u
                WHERE u.profil IN (2, 3)
                ORDER BY u.dateinscription DESC
                LIMIT 5
            ");
            $derniers_utilisateurs = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log('[dashboard] derniers_utilisateurs : ' . $e->getMessage());
        }

        try {
            $stmt = $pdo->query("
                SELECT name, city, subscription_active, created_at
                FROM restaurants
                ORDER BY created_at DESC
                LIMIT 5
            ");
            $derniers_restaurants = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log('[dashboard] derniers_restaurants : ' . $e->getMessage());
        }

        try {
            $stmt = $pdo->query("
                SELECT l.action_type, l.message, l.created_at, u.email
                FROM user_logs l
                LEFT JOIN utilisateurs u ON l.user_id = u.id
                ORDER BY l.created_at DESC
                LIMIT 5
            ");
            $derniers_logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log('[dashboard] derniers_logs : ' . $e->getMessage());
        }

        try {
            $nb_promos_actives = (int) $pdo->query("SELECT COUNT(*) FROM promos WHERE actif = 1")->fetchColumn();
        } catch (Exception $e) {
            error_log('[dashboard] nb_promos_actives : ' . $e->getMessage());
        }

        return compact(
            'nb_restaurants_total', 'nb_restaurants_actifs',
            'nb_utilisateurs', 'nb_plats',
            'nb_commandes_jour', 'ca_jour',
            'nb_promos_actives',
            'derniers_utilisateurs', 'derniers_restaurants', 'derniers_logs'
        );
    }

    // ---------------------------------------------------------------
    // Panel utilisateurs (clients + restaurateurs)
    // ---------------------------------------------------------------
    public function panel(): array
    {
        Auth::requireAdmin();

        $pdo   = Database::getInstance()->getConnection();
        $error = '';
        $users = [];

        // Traitement des actions POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {

            // Changement de role
            if ($_POST['action'] === 'update' && isset($_POST['user_id'], $_POST['profil'])) {
                $user_id    = (int) $_POST['user_id'];
                $new_profil = (int) $_POST['profil'];

                // 2 = restaurateur, 3 = client (la promotion en admin se fait via un autre flux)
                if (in_array($new_profil, [2, 3], true)) {

                    // Pour passer en client, les colonnes NOT NULL doivent avoir une valeur.
                    $extraData = [];
                    if ($new_profil === 3) {
                        $extraData = [
                            'civilite'     => 1,
                            'adresse_comp' => '',
                            'codepostal'   => '',
                            'ville'        => '',
                        ];
                    }

                    try {
                        (new User())->changeUserProfile($user_id, $new_profil, $extraData);
                        header('Location: ' . $GLOBALS['url'] . '/admin-panel');
                        exit();
                    } catch (Exception $e) {
                        $error = $e->getMessage();
                    }
                }
            }

            // Suppression
            if ($_POST['action'] === 'delete' && isset($_POST['user_id'], $_POST['profil_id'], $_POST['profil'])) {
                $profil_id = (int) $_POST['profil_id'];
                $profil    = (int) $_POST['profil'];

                try {
                    (new User())->deleteUser($profil_id, $profil);
                    header('Location: ' . $GLOBALS['url'] . '/admin-panel');
                    exit();
                } catch (PDOException $e) {
                    $error = "Erreur lors de la suppression.";
                }
            }
        }

        // Recuperation des utilisateurs
        try {
            $stmt = $pdo->prepare("
                SELECT
                    u.*,
                    COALESCE(r.nom, c.nom)             AS nom,
                    COALESCE(r.prenom, c.prenom)       AS prenom,
                    COALESCE(r.telephone, c.telephone) AS telephone
                FROM utilisateurs u
                LEFT JOIN restaurateurs r ON (u.profil = 2 AND u.profil_id = r.id)
                LEFT JOIN clients c       ON (u.profil = 3 AND u.profil_id = c.id)
                WHERE u.profil IN (2, 3)
                ORDER BY u.id ASC
            ");
            $stmt->execute();
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $error = "Erreur lors de la récupération des utilisateurs.";
        }

        return compact('error', 'users');
    }

    // ---------------------------------------------------------------
    // Gestion des restaurants (vue admin)
    // ---------------------------------------------------------------
    public function restaurants(): array
    {
        Auth::requireAdmin();

        $error                 = '';
        $success               = '';
        $restaurants           = [];
        $categories_restaurant = [];

        // Suppression d'un restaurant
        if ($_SERVER['REQUEST_METHOD'] === 'POST'
            && isset($_POST['action'], $_POST['id_restaurant'])
            && $_POST['action'] === 'delete'
        ) {
            $idToDelete = (int) $_POST['id_restaurant'];

            try {
                $restaurantModel = new Restaurant();
                if ($restaurantModel->delete($idToDelete)) {
                    header('Location: ' . $GLOBALS['url'] . '/admin-restaurants?success=deleted');
                    exit();
                } else {
                    $error = "La suppression du restaurant a échoué.";
                }
            } catch (PDOException $e) {
                error_log('[admin-restaurants] delete : ' . $e->getMessage());
                $error = "Erreur lors de la suppression. Détail technique : " . $e->getMessage();
            }
        }

        // Mise a jour de la categorie
        if ($_SERVER['REQUEST_METHOD'] === 'POST'
            && isset($_POST['action'], $_POST['id_restaurant'], $_POST['category_id'])
            && $_POST['action'] === 'update_category'
        ) {
            $idRestaurant = (int) $_POST['id_restaurant'];
            $idCategorie  = (int) $_POST['category_id'];

            try {
                $restaurantModel = new Restaurant();
                if ($restaurantModel->updateCategory($idRestaurant, $idCategorie)) {
                    header('Location: ' . $GLOBALS['url'] . '/admin-restaurants?success=updated');
                    exit();
                } else {
                    $error = "La mise à jour de la catégorie a échoué.";
                }
            } catch (PDOException $e) {
                error_log('[admin-restaurants] update_category : ' . $e->getMessage());
                $error = "Erreur lors de la modification de la catégorie. Détail technique : " . $e->getMessage();
            }
        }

        // Messages de succes (redirect)
        if (isset($_GET['success'])) {
            if ($_GET['success'] === 'deleted') {
                $success = "Restaurant supprimé avec succès.";
            } elseif ($_GET['success'] === 'updated') {
                $success = "Catégorie mise à jour avec succès.";
            }
        }

        // Chargement de la liste
        try {
            $restaurantModel       = new Restaurant();
            $restaurants           = $restaurantModel->listRestaurants(false);
            $categories_restaurant = (new Category())->listAll();
        } catch (PDOException $e) {
            $error = "Erreur lors du chargement des données.";
            error_log('[admin-restaurants] load : ' . $e->getMessage());
        }

        return compact('error', 'success', 'restaurants', 'categories_restaurant');
    }

    // ---------------------------------------------------------------
    // Ajouter un administrateur
    // ---------------------------------------------------------------
    public function ajouterAdmin(): array
    {
        Auth::requireAdmin();

        $message_success = '';
        $message_error   = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_admin'])) {

            $prenom = trim($_POST['prenom'] ?? '');
            $nom    = trim($_POST['nom'] ?? '');
            $email  = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
            $tel    = trim($_POST['telephone'] ?? '');
            $pass   = $_POST['password'] ?? '';
            $pass2  = $_POST['password2'] ?? '';

            if (empty($prenom) || empty($nom)) {
                $message_error = "Le prénom et le nom sont obligatoires.";
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $message_error = "Format d'email invalide.";
            } elseif ((new User())->isRegistered($email)) {
                $message_error = "Cet email est déjà utilisé par un autre compte.";
            } elseif (strlen($pass) < 8) {
                $message_error = "Le mot de passe doit contenir au moins 8 caractères.";
            } elseif ($pass !== $pass2) {
                $message_error = "Les mots de passe ne correspondent pas.";
            } else {

                $id_admin = (new User())->insertAdmin([
                    'nom'       => $nom,
                    'prenom'    => $prenom,
                    'telephone' => $tel,
                ]);

                if ($id_admin) {
                    $id_user = (new User())->insertUtilisateur([
                        'email'      => $email,
                        'motdepasse' => $pass,
                        'profil'     => 1,
                        'profil_id'  => $id_admin,
                    ]);

                    if ($id_user) {
                        $message_success = "Administrateur <strong>" . htmlspecialchars($prenom . ' ' . $nom) . "</strong> créé avec succès.";
                    } else {
                        $message_error = "Erreur lors de la création du compte utilisateur.";
                    }
                } else {
                    $message_error = "Erreur lors de la création de l'administrateur.";
                }
            }
        }

        return compact('message_success', 'message_error');
    }
}
