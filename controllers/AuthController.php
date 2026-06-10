<?php

class AuthController
{
    
    public function login()
    {
        $message_error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $pass  = $_POST['password'] ?? '';

            if (!empty($email) && !empty($pass)) {

                if ((new User())->tryToConnect($email, $pass)) {

                    // Redirection selon le profil
                    $profil = $_SESSION['user']['profil'];

                    if ($profil == 1) {
                        $redirect_url = 'dashboard';
                    } elseif ($profil == 2) {
                        $redirect_url = 'mon-compte-restaurateur';
                    } else {
                        $redirect_url = 'mon-compte';
                    }

                    header('Location: ' . APP_URL . '/' . $redirect_url);
                    exit();
                } else {
                    $message_error = "Identifiants invalides ou compte non activé.";
                }
            } else {
                $message_error = "Veuillez remplir tous les champs.";
            }
        }

        return compact('message_error');
    }

    
    // Déconnexion (aucune vue : on header()+exit())
    
    public function logout()
    {
        if (isset($_SESSION['user']['id'])) {
            (new UserLog())->log((int)$_SESSION["user"]["id"], "logout", 'Déconnexion du site');
        }
        $_SESSION = [];
        session_destroy();
        header('Location: ' . APP_URL . '/accueil');
        exit();
    }

    
    // Inscription Restaurateur (profil = 2)
    
    public function register()
    {
        $erreurs         = [];
        $succes          = false;
        $message_success = "Votre compte gérant a été créé avec succès. Bienvenue chez Miamy !";

        $prenom = '';
        $nom    = '';
        $email  = '';
        $tel    = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register_submit'])) {

            $prenom = sanitizeString($_POST['prenom'] ?? '');
            $nom    = sanitizeString($_POST['nom'] ?? '');
            $email  = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
            $tel    = sanitizeString($_POST['telephone'] ?? '');
            $pass   = $_POST['password'] ?? '';
            $pass2  = $_POST['password2'] ?? '';

            if (empty($prenom) || strlen($prenom) < 2 || strlen($prenom) > 50) {
                $erreurs[] = "Votre prénom doit contenir entre 2 et 50 caractères.";
            }
            if (empty($nom) || strlen($nom) < 2 || strlen($nom) > 50) {
                $erreurs[] = "Votre nom doit contenir entre 2 et 50 caractères.";
            }
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $erreurs[] = "Format d'email invalide.";
            } elseif ((new User())->isRegistered($email)) {
                $erreurs[] = "Cet email est déjà utilisé par un autre compte.";
            }
            if (empty($pass) || strlen($pass) < 8) {
                $erreurs[] = "Le mot de passe doit contenir au moins 8 caractères.";
            } elseif ($pass !== $pass2) {
                $erreurs[] = "Les mots de passe ne correspondent pas.";
            }

            if (empty($erreurs)) {

                // 1. Création du compte utilisateur (profil_id NULL temporairement)
                $id_user = (new User())->insertUtilisateur([
                    'email'      => $email,
                    'motdepasse' => $pass,
                    'profil'     => 2,
                    'profil_id'  => null,
                ]);

                if ($id_user) {
                    // 2. Création du restaurateur avec FK user_id
                    $restaurateurModel = new Restaurateur();
                    $id_restaurateur   = $restaurateurModel->insert([
                        'nom'       => $nom,
                        'prenom'    => $prenom,
                        'email'     => $email,
                        'telephone' => $tel,
                        'user_id'   => $id_user,
                    ]);

                    if ($id_restaurateur) {
                        // 3. Maj du profil_id côté utilisateurs (lien réciproque)
                        $pdo = Database::getInstance()->getConnection();
                        $pdo->prepare("UPDATE `utilisateurs` SET `profil_id` = :pid WHERE `id` = :id")
                            ->execute(['pid' => $id_restaurateur, 'id' => $id_user]);

                        $succes = true;
                        $prenom = $nom = $email = $tel = '';
                    } else {
                        $erreurs[] = "Impossible d'enregistrer vos informations professionnelles.";
                    }
                } else {
                    $erreurs[] = "Erreur lors de la création de vos identifiants de connexion.";
                }
            }
        }

        return compact('erreurs', 'succes', 'message_success', 'prenom', 'nom', 'email', 'tel');
    }

   
    // Inscription Client (profil = 3)
    
    public function registerClient()
    {
        $erreurs         = [];
        $succes          = false;
        $message_success = "Votre compte a été créé avec succès. Bienvenue chez Miamy !";

        $civilite     = '';
        $prenom       = '';
        $nom          = '';
        $email        = '';
        $tel          = '';
        $adresse      = '';
        $adresse_comp = '';
        $codepostal   = '';
        $ville        = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register_submit'])) {

            $civilite     = $_POST['civilite'] ?? '';
            $prenom       = sanitizeString($_POST['prenom'] ?? '');
            $nom          = sanitizeString($_POST['nom'] ?? '');
            $email        = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
            $tel          = sanitizeString($_POST['telephone'] ?? '');
            $adresse      = sanitizeString($_POST['adresse'] ?? '');
            $adresse_comp = sanitizeString($_POST['adresse_comp'] ?? '');
            $pass         = $_POST['password'] ?? '';
            $pass2        = $_POST['password2'] ?? '';
            $codepostal   = $_POST['codepostal'] ?? '';
            $ville        = $_POST['ville'] ?? '';

            if (!in_array($civilite, ['1', '2', '3'], true)) {
                $erreurs[] = "Veuillez sélectionner une civilité.";
            }
            if (empty($prenom) || strlen($prenom) < 2 || strlen($prenom) > 50) {
                $erreurs[] = "Votre prénom doit contenir entre 2 et 50 caractères.";
            }
            if (empty($nom) || strlen($nom) < 2 || strlen($nom) > 50) {
                $erreurs[] = "Votre nom doit contenir entre 2 et 50 caractères.";
            }
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $erreurs[] = "Format d'email invalide.";
            } elseif ((new User())->isRegistered($email)) {
                $erreurs[] = "Cet email est déjà utilisé par un autre compte.";
            }
            if (empty($tel)) {
                $erreurs[] = "Le numéro de téléphone est obligatoire";
            }
            if (empty($adresse)) {
                $erreurs[] = "L'adresse est obligatoire.";
            }
            if (empty($pass) || strlen($pass) < 8) {
                $erreurs[] = "Le mot de passe doit contenir au moins 8 caractères.";
            } elseif ($pass !== $pass2) {
                $erreurs[] = "Les mots de passe ne correspondent pas.";
            }
            if (!preg_match('/^\d{5}$/', $codepostal)) {
                $erreurs[] = "Le code postal doit contenir 5 chiffres.";
            }
            if (empty($ville)) {
                $erreurs[] = "La ville est obligatoire.";
            }

            if (empty($erreurs)) {

                // 1. Création du compte utilisateur (profil_id NULL temporairement)
                $id_user = (new User())->insertUtilisateur([
                    'email'      => $email,
                    'motdepasse' => $pass,
                    'profil'     => 3,
                    'profil_id'  => null,
                ]);

                if ($id_user) {
                    // 2. Création du client avec FK user_id
                    $clientModel = new Client();
                    $id_client   = $clientModel->insert([
                        'civilite'     => $civilite,
                        'nom'          => $nom,
                        'prenom'       => $prenom,
                        'telephone'    => $tel,
                        'adresse'      => $adresse,
                        'adresse_comp' => $adresse_comp,
                        'codepostal'   => $codepostal,
                        'ville'        => $ville,
                        'user_id'      => $id_user,
                    ]);

                    if ($id_client) {
                        // 3. Maj du profil_id côté utilisateurs (lien réciproque)
                        $pdo = Database::getInstance()->getConnection();
                        $pdo->prepare("UPDATE `utilisateurs` SET `profil_id` = :pid WHERE `id` = :id")
                            ->execute(['pid' => $id_client, 'id' => $id_user]);

                        $succes = true;
                        $civilite = $prenom = $nom = $email = $tel = $adresse = $adresse_comp = $codepostal = $ville = '';
                    } else {
                        $erreurs[] = "Impossible d'enregistrer vos informations.";
                    }
                } else {
                    $erreurs[] = "Erreur lors de la création de vos identifiants de connexion.";
                }
            }
        }

        return compact(
            'erreurs', 'succes', 'message_success',
            'civilite', 'prenom', 'nom', 'email', 'tel',
            'adresse', 'adresse_comp', 'codepostal', 'ville'
        );
    }
}
