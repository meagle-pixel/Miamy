<?php  

// Initialisation des messages
$message_success = '';
$message_error = '';

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register_submit'])) {
    
	
	
    // 1. Récupération et assainissement des données
    $prenom = sanitizeString($_POST['prenom']);
    $nom    = sanitizeString($_POST['nom']);
    $email  = sanitizeString($_POST['email']);
    $tel    = sanitizeString($_POST['telephone']);
    $pass   = $_POST['password']; 

    // 2. Vérifications de sécurité
    if (empty($prenom) || empty($nom) || empty($email) || empty($pass)) {
        $message_error = "Veuillez remplir tous les champs obligatoires (*).";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message_error = "Format d'email invalide.";
    } elseif (isRegistered($email)) {
        $message_error = "Cet email est déjà utilisé par un autre compte.";
    } else {
        
        // 3. Insertion dans la table 'restaurateurs' pour les infos profil
        $data_resto = [
            'nom'       => $nom,
            'prenom'    => $prenom,
            'email'     => $email,
            'telephone' => $tel
        ];
        
        $id_restaurateur = insertRestaurateur($data_resto);

        if ($id_restaurateur) {
            
            // 4. Création du compte de connexion via TA fonction
            // Le profil 2 correspond aux restaurateurs dans ton système
            $user_account = [
                'email'      => $email,
                'motdepasse' => $pass, // Sera haché avec l'email et le sel par ta fonction
                'profil'     => 2,     
                'profil_id'  => $id_restaurateur
            ];
            
            $id_user = insertUtilisateur($user_account);
            
            if ($id_user) {
                $message_success = "Votre compte gérant a été créé avec succès. Bienvenue chez Miamy !";
            } else {
                // Si l'utilisateur échoue, on a un gérant orphelin (pense à le supprimer ou le gérer)
                $message_error = "Erreur lors de la création de vos identifiants de connexion.";
            }
        } else {
            $message_error = "Impossible d'enregistrer vos informations professionnelles.";
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

                    <?php if($message_success): ?>
                        <div class="alert alert-success shadow-sm"><?= $message_success ?></div>
                        <div class="text-center mt-3">
                            <a href="connexion" class="btn btn_theme">Se connecter maintenant</a>
                        </div>
                    <?php else: ?>
                        
                        <?php if($message_error): ?>
                            <div class="alert alert-danger shadow-sm"><?= $message_error ?></div>
                        <?php endif; ?>

                        <div class="common_author_form">
                            <form action="<?= $GLOBALS['url'] ?>/inscription" method="POST">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group mb-3">
                                            <input type="text" name="prenom" class="form-control" placeholder="Prénom*" required />
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group mb-3">
                                            <input type="text" name="nom" class="form-control" placeholder="Nom de famille*" required />
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group mb-3">
                                            <input type="email" name="email" class="form-control" placeholder="Email (identifiant)*" required />
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group mb-3">
                                            <input type="text" name="telephone" class="form-control" placeholder="Téléphone*" required />
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group mb-4">
                                            <input type="password" name="password" class="form-control" placeholder="Mot de passe*" required />
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