<?php
/** @var string $message_error */
$message_error = $message_error ?? '';
?>

<section id="common_banner">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="common_bannner_text">
                    <h2>Se connecter</h2>
                    <ul>
                        <li><a href="index.php">Accueil</a></li>
                        <li><span><i class="fas fa-circle"></i></span>Se connecter</li>
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
                        <h3>Accès à votre espace Miamy</h3>
                        <h2>Connectez-vous</h2>
                    </div>

                    <?php if ($message_error): ?>
                        <div class="alert alert-danger shadow-sm"><?= $message_error ?></div>
                    <?php endif; ?>

                    <div class="common_author_form">
                        <form action="connexion" method="POST" id="main_author_form">
                            <div class="form-group">
                                <label for="email" class="visually-hidden">Email</label>
                                <input type="email" name="email" id="email" class="form-control" placeholder="Email" required />
                            </div>
                            <div class="form-group">
                                <label for="password" class="visually-hidden">Mot de passe</label>
                                <input type="password" name="password" id="password" class="form-control" placeholder="Mot de passe" required />
                                <a href="forgot-password">Mot de passe oublié ?</a>
                            </div>
                            <div class="common_form_submit">
                                <button type="submit" class="btn btn_theme btn_md">Se connecter</button>
                            </div>
                            <div class="have_acount_area">
                                <p>Pas encore de compte ? <a href="inscription">S'enregistrer</a></p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
