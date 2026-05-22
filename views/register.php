<?php
/** @var array  $erreurs         */
/** @var bool   $succes          */
/** @var string $message_success */
/** @var string $prenom          */
/** @var string $nom             */
/** @var string $email           */
/** @var string $tel             */

// Valeurs par defaut au cas ou la vue serait appelee sans controller
$erreurs         = $erreurs         ?? [];
$succes          = $succes          ?? false;
$message_success = $message_success ?? "Votre compte gérant a été créé avec succès. Bienvenue chez Miamy !";
$prenom          = $prenom          ?? '';
$nom             = $nom             ?? '';
$email           = $email           ?? '';
$tel             = $tel             ?? '';
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
