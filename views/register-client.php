<?php

// Initialisation
$erreurs        = [];
$succes         = false;
$message_success = "Votre compte a été créé avec succès. Bienvenue chez Miamy !";

$civilite = '';
$prenom = '';
$nom    = '';
$email  = '';
$tel    = '';
$adresse = '';
$adresse_comp = '';
$pass = '';
$pass2 = '';
$codepostal = '';
$ville = '';


// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register_submit'])) {

    // 1. Récupération et assainissement des données
    $civilite = $_POST['civilite'] ?? '';
    $prenom   = sanitizeString($_POST['prenom'] ?? '');
    $nom      = sanitizeString($_POST['nom'] ?? '');
    $email    = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
    $tel      = sanitizeString($_POST['telephone'] ?? '');
    $adresse = sanitizeString($_POST['adresse'] ?? '');
    $adresse_comp = sanitizeString($_POST['adresse_comp'] ?? '');
    $pass     = $_POST['password'] ?? '';
    $pass2    = $_POST['password2'] ?? '';
    $codepostal = $_POST['codepostal'] ?? '';
    $ville = $_POST['ville'] ?? '';

    // 2. Vérifications de sécurité

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
    } elseif (isRegistered($email)) {
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

    // 3. Si aucune erreur, on insère
    if (empty($erreurs)) {

        $data_client = [
            'civilite'     => $civilite,
            'nom'          => $nom,
            'prenom'       => $prenom,
            'telephone'    => $tel,
            'adresse'      => $adresse,
            'adresse_comp' => $adresse_comp,
            'codepostal'   => $codepostal,
            'ville'        => $ville,
        ];

        $id_client = insertClient($data_client);

        if ($id_client) {

            $user_account = [
                'email'      => $email,
                'motdepasse' => $pass,
                'profil'     => 3,
                'profil_id'  => $id_client
            ];

            $id_user = insertUtilisateur($user_account);

            if ($id_user) {
                $succes = true;
                // On vide les champs après succès
                $civilite = $prenom = $nom = $email = $tel = $adresse = $adresse_comp = $codepostal = $ville = '';
            } else {
                $erreurs[] = "Erreur lors de la création de vos identifiants de connexion.";
            }
        } else {
            $erreurs[] = "Impossible d'enregistrer vos informations.";
        }
    }
}
?>

<section id="common_banner">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="common_bannner_text">
                    <h2>Inscription Client</h2>
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
                        <h3>Rejoignez-nous !</h3>
                        <h2>Créer votre compte Miamy</h2>
                    </div>

                    <?php if ($succes): ?>
                        <div class="alert alert-success shadow-sm"><?= $message_success ?></div>
                        <div class="text-center mt-3">
                            <a href="connexion" class="btn btn_theme">Se connecter maintenant</a>
                        </div>
                    <?php else: ?>

                        <?php if (!empty($erreurs)): ?>
                            <div class="alert alert-danger shadow-sm">
                                <ul class="mb-0">
                                    <?php foreach ($erreurs as $err): ?>
                                        <li><?= htmlspecialchars($err) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <div class="common_author_form">
                            <form action="<?= $GLOBALS['url'] ?>/inscription-client" method="POST">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="form-group mb-3">
                                            <select name="civilite" id="civilite">
                                                <option value="1" <?= $civilite === '1' ? 'selected' : '' ?>>M.</option>
                                                <option value="2" <?= $civilite === '2' ? 'selected' : '' ?>>Mme</option>
                                                <option value="3" <?= $civilite === '3' ? 'selected' : '' ?>>Mlle</option>
                                            </select>
                                        </div>
                                    </div>
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
                                            <input type="text" name="adresse" class="form-control" placeholder="Votre adresse" value="<?= htmlspecialchars($adresse) ?>" required />
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group mb-3">
                                            <input type="text" name="adresse_comp" class="form-control" placeholder="Adresse complémentaire" value="<?= htmlspecialchars($adresse_comp) ?>" />
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group mb-3">
                                            <input type="text" name="codepostal" class="form-control" placeholder="Code postal" value="<?= htmlspecialchars($codepostal) ?>" />
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group mb-3">
                                            <input type="text" name="ville" class="form-control" placeholder="Ville" value="<?= htmlspecialchars($ville) ?>" />
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
                                            <button type="submit" name="register_submit" class="btn btn_theme btn_md w-100">Créer mon compte</button>
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