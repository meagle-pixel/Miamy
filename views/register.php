<?php

// Initialisation
$erreurs        = [];
$succes         = false;
$message_success = "Votre compte gérant a été créé avec succès. Bienvenue chez Miamy !";

$prenom = '';
$nom    = '';
$email  = '';
$tel    = '';

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register_submit'])) {

    // 1. Récupération et assainissement des données
    $prenom   = sanitizeString($_POST['prenom'] ?? '');
    $nom      = sanitizeString($_POST['nom'] ?? '');
    $email    = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
    $tel      = sanitizeString($_POST['telephone'] ?? '');
    $pass     = $_POST['password'] ?? '';
    $pass2    = $_POST['password2'] ?? '';

    // 2. Vérifications de sécurité
    if (empty($prenom) || strlen($prenom) < 2 || strlen($prenom) > 50) {
        $erreurs[] = "Votre prénom doit contenir entre 2 et 50 caractères.";
    }

    if (empty($nom) || strlen($nom) < 2 || strlen($nom) > 50) {
        $erreurs[] = "Votre nom doit contenir entre 2 et 50 caractères.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erreurs[] = "Format d'email invalide.";
    } elseif (isRegistered($email)) {
        $erreurs[] = "Cet email est déjà utilisé par un autre compte.";
    }

    if (empty($pass) || strlen($pass) < 8) {
        $erreurs[] = "Le mot de passe doit contenir au moins 8 caractères.";
    } elseif ($pass !== $pass2) {
        $erreurs[] = "Les mots de passe ne correspondent pas.";
    }

    // 3. Si aucune erreur, on insère
    if (empty($erreurs)) {

        $data_resto = [
            'nom'       => $nom,
            'prenom'    => $prenom,
            'email'     => $email,
            'telephone' => $tel
        ];

        $id_restaurateur = insertRestaurateur($data_resto);

        if ($id_restaurateur) {

            $user_account = [
                'email'      => $email,
                'motdepasse' => $pass,
                'profil'     => 2,
                'profil_id'  => $id_restaurateur
            ];

            $id_user = insertUtilisateur($user_account);

            if ($id_user) {
                $succes = true;
                // On vide les champs après succès
                $prenom = $nom = $email = $tel = '';
            } else {
                $erreurs[] = "Erreur lors de la création de vos identifiants de connexion.";
            }
        } else {
            $erreurs[] = "Impossible d'enregistrer vos informations professionnelles.";
        }
    }
}
?>

<section id="common_banner">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="common_bannner_text">
                    <h2>Inscription Restaurateur</h2>
                    <ul>
                        <li><a href="">Accueil</a></li>
                        <li><span><i class="fas fa-circle"></i></span>Inscription</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section_padding">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 offset-lg-2">
                <div class="common_author_boxed">
                    <div class="common_author_heading">
                        <h3>Devenez partenaire</h3>
                        <h2>Créer mon accès Miamy</h2>
                    </div>

                    <?php if($succes): ?>
                        <div class="alert alert-success shadow-sm"><?= $message_success ?></div>
                        <div class="text-center mt-3">
                            <a href="connexion" class="btn btn_theme">Se connecter maintenant</a>
                        </div>
                    <?php else: ?>

                        <?php if(!empty($erreurs)): ?>
                            <div class="alert alert-danger shadow-sm">
                                <ul class="mb-0">
                                    <?php foreach($erreurs as $err): ?>
                                        <li><?= htmlspecialchars($err) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <div class="common_author_form">
                            <form action="<?= $GLOBALS['url'] ?>/inscription" method="POST">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group mb-3">
                                            <input type="text" name="prenom" class="form-control" placeholder="Prénom*" value="<?= htmlspecialchars($prenom) ?>" required />
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group mb-3">
                                            <input type="text" name="nom" class="form-control" placeholder="Nom de famille*" value="<?= htmlspecialchars($nom) ?>" required />
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group mb-3">
                                            <input type="email" name="email" class="form-control" placeholder="Email (identifiant)*" value="<?= htmlspecialchars($email) ?>" required />
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group mb-3">
                                            <input type="text" name="telephone" class="form-control" placeholder="Téléphone" value="<?= htmlspecialchars($tel) ?>" />
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group mb-3">
                                            <input type="password" name="password" class="form-control" placeholder="Mot de passe* (8 caractères min.)" required />
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group mb-4">
                                            <input type="password" name="password2" class="form-control" placeholder="Confirmer le mot de passe*" required />
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="common_form_submit">
                                            <button type="submit" name="register_submit" class="btn btn_theme btn_md w-100">Créer mon compte gérant</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>