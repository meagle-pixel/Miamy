<?php
/** @var string $message_success */
/** @var string $message_error   */

// Valeurs par defaut au cas ou la vue serait appelee sans controller
$message_success = $message_success ?? '';
$message_error   = $message_error   ?? '';
?>

<div class="container-fluid px-4">

    <h1 class="mt-4">Ajouter un administrateur</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="<?= APP_URL ?>/accueil">Accueil</a></li>
        <li class="breadcrumb-item"><a href="<?= APP_URL ?>/dashboard">Admin</a></li>
        <li class="breadcrumb-item active">Ajouter un admin</li>
    </ol>

    <div class="row">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0" style="font-size:15px; font-weight:600;">
                        <i class="fas fa-user-shield me-2" style="color:#4361EE;"></i>
                        Nouveau compte administrateur
                    </h5>
                </div>
                <div class="card-body">

                    <?php if ($message_success): ?>
                        <div class="alert alert-success"><?= $message_success ?></div>
                        <a href="<?= APP_URL ?>/dashboard" class="btn btn-primary btn-sm">Retour au dashboard</a>
                    <?php else: ?>

                        <?php if ($message_error): ?>
                            <div class="alert alert-danger"><?= htmlspecialchars($message_error) ?></div>
                        <?php endif; ?>

                        <form method="POST" action="">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Prénom *</label>
                                    <input type="text" name="prenom" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Nom *</label>
                                    <input type="text" name="nom" class="form-control" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Email *</label>
                                    <input type="email" name="email" class="form-control" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Téléphone</label>
                                    <input type="text" name="telephone" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Mot de passe * <small class="text-muted">(8 car. min.)</small></label>
                                    <input type="password" name="password" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Confirmer *</label>
                                    <input type="password" name="password2" class="form-control" required>
                                </div>
                                <div class="col-12 mt-2">
                                    <button type="submit" name="submit_admin" class="btn btn-primary w-100">
                                        <i class="fas fa-user-plus me-2"></i> Créer l'administrateur
                                    </button>
                                </div>
                            </div>
                        </form>

                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

</div>
