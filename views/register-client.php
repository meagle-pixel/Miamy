<?php
/** @var array  $erreurs         */
/** @var bool   $succes          */
/** @var string $message_success */
/** @var string $civilite        */
/** @var string $prenom          */
/** @var string $nom             */
/** @var string $email           */
/** @var string $tel             */
/** @var string $adresse         */
/** @var string $adresse_comp    */
/** @var string $codepostal      */
/** @var string $ville           */

// Valeurs par defaut au cas ou la vue serait appelee sans controller
$erreurs         = $erreurs         ?? [];
$succes          = $succes          ?? false;
$message_success = $message_success ?? "Votre compte a été créé avec succès. Bienvenue chez Miamy !";
$civilite        = $civilite        ?? '';
$prenom          = $prenom          ?? '';
$nom             = $nom             ?? '';
$email           = $email           ?? '';
$tel             = $tel             ?? '';
$adresse         = $adresse         ?? '';
$adresse_comp    = $adresse_comp    ?? '';
$codepostal      = $codepostal      ?? '';
$ville           = $ville           ?? '';
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
                            <form action="<?= APP_URL ?>/inscription-client" method="POST">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="form-group mb-3">
                                            <label for="civilite" class="visually-hidden">Civilité</label>
                                            <select name="civilite" id="civilite">
                                                <option value="1" <?= $civilite === '1' ? 'selected' : '' ?>>M.</option>
                                                <option value="2" <?= $civilite === '2' ? 'selected' : '' ?>>Mme</option>
                                                <option value="3" <?= $civilite === '3' ? 'selected' : '' ?>>Mlle</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group mb-3">
                                            <label for="prenom" class="visually-hidden">Prénom</label>
                                            <input type="text" name="prenom" id="prenom" class="form-control" placeholder="Prénom*" value="<?= htmlspecialchars($prenom) ?>" required />
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group mb-3">
                                            <label for="nom" class="visually-hidden">Nom de famille</label>
                                            <input type="text" name="nom" id="nom" class="form-control" placeholder="Nom de famille*" value="<?= htmlspecialchars($nom) ?>" required />
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group mb-3">
                                            <label for="email" class="visually-hidden">Email (identifiant)</label>
                                            <input type="email" name="email" id="email" class="form-control" placeholder="Email (identifiant)*" value="<?= htmlspecialchars($email) ?>" required />
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group mb-3">
                                            <label for="telephone" class="visually-hidden">Téléphone</label>
                                            <input type="text" name="telephone" id="telephone" class="form-control" placeholder="Téléphone" value="<?= htmlspecialchars($tel) ?>" />
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group mb-3">
                                            <label for="adresse" class="visually-hidden">Adresse</label>
                                            <input type="text" name="adresse" id="adresse" class="form-control" placeholder="Votre adresse" value="<?= htmlspecialchars($adresse) ?>" required />
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group mb-3">
                                            <label for="adresse_comp" class="visually-hidden">Adresse complémentaire</label>
                                            <input type="text" name="adresse_comp" id="adresse_comp" class="form-control" placeholder="Adresse complémentaire" value="<?= htmlspecialchars($adresse_comp) ?>" />
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group mb-3">
                                            <label for="codepostal" class="visually-hidden">Code postal</label>
                                            <input type="text" name="codepostal" id="codepostal" class="form-control" placeholder="Code postal" value="<?= htmlspecialchars($codepostal) ?>" />
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group mb-3">
                                            <label for="ville" class="visually-hidden">Ville</label>
                                            <input type="text" name="ville" id="ville" class="form-control" placeholder="Ville" value="<?= htmlspecialchars($ville) ?>" />
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group mb-3">
                                            <label for="password" class="visually-hidden">Mot de passe</label>
                                            <input type="password" name="password" id="password" class="form-control" placeholder="Mot de passe* (8 caractères min.)" required />
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group mb-4">
                                            <label for="password2" class="visually-hidden">Confirmer le mot de passe</label>
                                            <input type="password" name="password2" id="password2" class="form-control" placeholder="Confirmer le mot de passe*" required />
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
