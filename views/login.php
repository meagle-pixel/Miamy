<?php
$message_error = "";

// 2. Traitement de la connexion
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = mysqli_real_escape_string(Database::getInstance()->getConnection(), $_POST['email']);
    $pass  = $_POST['password'];

    if (!empty($email) && !empty($pass)) {
        // On utilise TA fonction de connexion
        if (trytoconnect($email, $pass)) {

            // Redirection selon le profil
            $profil = $_SESSION['user']['profil'];

            $redirect_url = ($profil == 1 || $profil == 2) ? 'mon-compte-restaurateur' : 'mon-compte';

            // Redirection JavaScript
            echo "<script type='text/javascript'>window.location.href='" . $GLOBALS['url'] . "/" . $redirect_url . "';</script>";
            exit();
        } else {
            $message_error = "Identifiants invalides ou compte non activé.";
        }
    } else {
        $message_error = "Veuillez remplir tous les champs.";
    }
}
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
                                <input type="email" name="email" class="form-control" placeholder="Email" required />
                            </div>
                            <div class="form-group">
                                <input type="password" name="password" class="form-control" placeholder="Mot de passe" required />
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