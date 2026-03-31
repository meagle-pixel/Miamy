<?php
$message_success = '';
$message_error   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message_error = "Veuillez saisir une adresse email valide.";
    } else {
        requestPasswordReset($email);
        // On affiche toujours un message de succès (sécurité : ne pas révéler si l'email existe)
        $message_success = "Si un compte est associé à cet email, vous recevrez un lien de réinitialisation dans quelques minutes.";
    }
}
?>

<section id="common_banner">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="common_bannner_text">
                    <h2>Mot de passe oublié</h2>
                    <ul>
                        <li><a href="accueil">Accueil</a></li>
                        <li><span><i class="fas fa-circle"></i></span>Mot de passe oublié</li>
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
                        <h3>Mot de passe oublié</h3>
                        <h2>Réinitialiser votre mot de passe</h2>
                    </div>

                    <?php if ($message_success): ?>
                        <div class="alert alert-success shadow-sm">
                            <i class="fas fa-check-circle me-2"></i> <?= $message_success ?>
                        </div>
                        <div class="text-center mt-3">
                            <a href="connexion" class="btn btn_theme btn_md">Retour à la connexion</a>
                        </div>
                    <?php else: ?>

                        <?php if ($message_error): ?>
                            <div class="alert alert-danger shadow-sm"><?= $message_error ?></div>
                        <?php endif; ?>

                        <div class="common_author_form">
                            <form action="forgot-password" method="POST" id="main_author_form">
                                <div class="form-group">
                                    <input type="email" name="email" class="form-control" placeholder="Votre adresse email" required />
                                </div>
                                <div class="common_form_submit">
                                    <button type="submit" class="btn btn_theme btn_md">Réinitialiser mon mot de passe</button>
                                </div>
                                <div class="have_acount_area">
                                    <p>Vous vous souvenez de votre mot de passe ? <a href="connexion">Se connecter</a></p>
                                </div>
                            </form>
                        </div>

                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>
