<?php

class UserController
{
    // Mon compte (client — vue statique, rien à préparer)
    
    public function monCompte()
    {
        return [];
    }

   
    // Mon compte restaurateur
    
    public function monCompteRestaurateur()
    {
        if (!isset($_SESSION['connected']) || $_SESSION['connected'] !== true || $_SESSION['user']['profil'] > 2) {
            header('Location: ' . APP_URL . '/connexion');
            exit();
        }

        $restaurateur    = $_SESSION['user-info'] ?? [];
        $id_restaurateur = $_SESSION['user']['profil_id'] ?? null;

        $restoClass = new Restaurant();
        $mesRestos  = $restoClass->listByOwner((int)$id_restaurateur);

        $message_success = '';
        if (isset($_GET['success']) && $_GET['success'] === 'deleted') {
            $message_success = "Le restaurant a été supprimé avec succès.";
        }

        return compact('restaurateur', 'mesRestos', 'message_success');
    }

    
    // Profil — éditer
    
    public function profilEditer()
    {
        if (!isset($_SESSION['connected']) || $_SESSION['connected'] !== true) {
            header('Location: ' . APP_URL . '/connexion');
            exit();
        }

        $id_restaurateur = $_SESSION['user']['profil_id'];
        $pdo             = Database::getInstance()->getConnection();
        $message_success = '';
        $message_error   = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_profil'])) {

            $nom       = trim($_POST['nom'] ?? '');
            $prenom    = trim($_POST['prenom'] ?? '');
            $email     = trim($_POST['email'] ?? '');
            $telephone = trim($_POST['telephone'] ?? '');

            if (empty($nom) || empty($prenom) || empty($email)) {
                $message_error = "Le nom, prénom et email sont obligatoires.";
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $message_error = "L'adresse email n'est pas valide.";
            } else {
                $restaurateurModel = new Restaurateur();
                $ok = $restaurateurModel->update([
                    'id'        => $id_restaurateur,
                    'nom'       => $nom,
                    'prenom'    => $prenom,
                    'email'     => $email,
                    'telephone' => $telephone,
                ]);

                if ($ok) {
                    // Synchroniser l'email cote table utilisateurs
                    $stmt = $pdo->prepare(
                        "UPDATE utilisateurs SET email = ? WHERE profil_id = ? AND profil = 2"
                    );
                    $stmt->execute([$email, $id_restaurateur]);

                    $_SESSION['user-info']['nom']       = $nom;
                    $_SESSION['user-info']['prenom']    = $prenom;
                    $_SESSION['user-info']['email']     = $email;
                    $_SESSION['user-info']['telephone'] = $telephone;
                    $_SESSION['user']['email']          = $email;

                    $message_success = "Votre profil a été mis à jour avec succès.";
                } else {
                    $message_error = "Une erreur est survenue lors de la mise à jour.";
                }
            }
        }

        $restaurateur = $_SESSION['user-info'];

        return compact('restaurateur', 'message_success', 'message_error');
    }

    
    // Profile (client — vue statique, rien à préparer)
    
    public function profile()
    {
        return [];
    }
}
