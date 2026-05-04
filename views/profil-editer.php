<?php
/** @var array  $restaurateur    */
/** @var string $message_success */
/** @var string $message_error   */
?>

<section id="common_banner">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="common_bannner_text">
                    <h2>Mon profil</h2>
                    <ul>
                        <li><a href="accueil">Accueil</a></li>
                        <li><span><i class="fas fa-circle"></i></span><a href="mon-compte-restaurateur">Mon compte</a></li>
                        <li><span><i class="fas fa-circle"></i></span>Modifier mon profil</li>
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
                        <h3>Mon espace</h3>
                        <h2>Modifier mon profil</h2>
                    </div>

                    <?php if ($message_success): ?>
                        <div class="alert alert-success shadow-sm">
                            <i class="fas fa-check-circle me-2"></i> <?= $message_success ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($message_error): ?>
                        <div class="alert alert-danger shadow-sm"><?= $message_error ?></div>
                    <?php endif; ?>

                    <div class="common_author_form">
                        <form action="profil-editer" method="POST">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group mb-3">
                                        <label>Prénom *</label>
                                        <input type="text" name="prenom" class="form-control"
                                            value="<?= htmlspecialchars($restaurateur['prenom'] ?? '') ?>" required>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group mb-3">
                                        <label>Nom *</label>
                                        <input type="text" name="nom" class="form-control"
                                            value="<?= htmlspecialchars($restaurateur['nom'] ?? '') ?>" required>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group mb-3">
                                        <label>Email *</label>
                                        <input type="email" name="email" class="form-control"
                                            value="<?= htmlspecialchars($restaurateur['email'] ?? '') ?>" required>
                                        <small class="text-muted">Cet email est aussi utilisé pour vous connecter.</small>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group mb-4">
                                        <label>Téléphone</label>
                                        <input type="text" name="telephone" class="form-control"
                                            value="<?= htmlspecialchars($restaurateur['telephone'] ?? '') ?>"
                                            placeholder="06 00 00 00 00">
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <a href="mon-compte-restaurateur" class="btn btn-outline-secondary w-100">
                                        <i class="fas fa-arrow-left me-2"></i> Retour
                                    </a>
                                </div>
                                <div class="col-lg-6">
                                    <button type="submit" name="submit_profil" class="btn btn_theme btn_md w-100">
                                        <i class="fas fa-save me-2"></i> Enregistrer
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
</section>
