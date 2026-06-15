<?php
/** @var array  $restaurateur    */
/** @var array  $mesRestos       */
/** @var string $message_success */
?>

<section id="common_banner">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="common_bannner_text">
                    <h2>Mon Espace Gérant</h2>
                    <ul>
                        <li><a href="accueil">Accueil</a></li>
                        <li><span><i class="fas fa-circle"></i></span>Tableau de bord</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<section id="dashboard_main_arae" class="section_padding">
    <div class="container">
        <div class="row">
            <div class="col-lg-4">
                <div class="dashboard_sidebar shadow-sm p-4 bg-white rounded">
                    <div class="dashboard_sidebar_user text-center mb-4">
                        <img src="<?= APP_URL ?>/assets/img/common/user-placeholder.png" alt="" class="rounded-circle mb-3" style="width:100px;">
                        <h3><?= htmlspecialchars(($restaurateur['prenom'] ?? '') . ' ' . ($restaurateur['nom'] ?? 'Administrateur')) ?></h3>
                        <p class="text-muted"><?= htmlspecialchars($restaurateur['email'] ?? $_SESSION['user']['email'] ?? '') ?></p>
                    </div>
                    <div class="dashboard_menu">
                        <ul class="list-unstyled">
                            <li class="mb-2">
                                <a href="mon-compte-restaurateur" class="btn btn_theme w-100 text-start">
                                    <i class="fas fa-tachometer-alt me-2"></i> Mes établissements
                                </a>
                            </li>
                            <li class="mb-2">
                                <a href="profil-editer" class="btn btn_theme w-100 text-start">
                                    <i class="fas fa-user-edit me-2"></i> Modifier mon profil
                                </a>
                            </li>
                            <li class="mb-2">
                                <a href="deconnexion" class="btn w-100 text-start btn-deconnexion">
                                    <i class="fas fa-sign-out-alt me-2"></i> Déconnexion
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="dashboard_main_content">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="section_heading">
                            <h2>Mes Établissements</h2>
                        </div>
                        <a href="ajouter-restaurant" class="btn btn_theme btn_md">
                            <i class="fas fa-plus me-2"></i> Ajouter un restaurant
                        </a>
                    </div>

                    <?php if ($message_success): ?>
                        <div class="alert alert-success shadow-sm">
                            <i class="fas fa-check-circle me-2"></i> <?= $message_success ?>
                        </div>
                    <?php endif; ?>

                    <?php if (empty($mesRestos)): ?>
                        <div class="alert alert-info text-center py-5 shadow-sm">
                            <i class="fas fa-utensils fa-3x mb-3 text-muted"></i>
                            <h4>Vous n'avez pas encore enregistré d'établissement.</h4>
                            <p>Commencez dès maintenant en ajoutant votre premier restaurant.</p>
                            <a href="ajouter-restaurant" class="btn btn_theme mt-3">Créer ma fiche</a>
                        </div>
                    <?php else: ?>
                        <div class="row">
                            <?php foreach ($mesRestos as $resto): ?>
                                <div class="col-lg-12 mb-4">
                                    <div class="restaurant_card_admin d-md-flex align-items-center bg-white shadow-sm rounded overflow-hidden p-3 border">
                                        <div class="resto_img me-md-4 mb-3 mb-md-0">
                                            <img src="<?= APP_URL ?>/assets/img/restaurants/<?= !empty($resto['main_image']) ? $resto['main_image'] : 'default-resto.jpg' ?>"
                                                alt="" class="rounded" style="width:150px; height:100px; object-fit:cover;">
                                        </div>
                                        <div class="resto_info flex-grow-1">
                                            <h4 class="mb-1">
                                                <a href="gestion-carte.php"><?= htmlspecialchars($resto['name']) ?></a>
                                            </h4>
                                            <p class="text-muted mb-2"><i class="fas fa-map-marker-alt me-1"></i> <?= htmlspecialchars($resto['city']) ?></p>

                                            <?php if ($resto['subscription_active']): ?>
                                                <span class="badge bg-success mb-3"><i class="fas fa-check me-1"></i> Abonnement Actif</span>
                                            <?php else: ?>
                                                <span class="badge bg-warning text-dark mb-3"><i class="fas fa-clock me-1"></i> En attente de paiement</span>
                                            <?php endif; ?>

                                            <div class="resto_actions">
                                                <a href="gestion-carte?id=<?= $resto['id_restaurant'] ?>" class="btn btn_theme btn_sm me-2">
                                                    <i class="fas fa-book-open me-1"></i> Gérer la carte
                                                </a>
                                                <a href="configurer-qr-codes/<?= $resto['id_restaurant'] ?>" class="btn btn_navber btn_sm me-2">
                                                    <i class="fas fa-qrcode me-1"></i> QR Codes
                                                </a>
                                                <a href="details?id=<?= $resto['id_restaurant'] ?>" class="btn btn-info btn_sm me-2">
                                                    <i class="fas fa-info-circle me-1"></i> Détails
                                                </a>
                                                <a href="modifier-restaurant?id=<?= $resto['id_restaurant'] ?>" class="btn btn-warning btn_sm me-2">
                                                    <i class="fas fa-edit me-1"></i> Modifier
                                                </a>
                                                <a href="supprimer-restaurant?id=<?= $resto['id_restaurant'] ?>" class="btn btn-danger btn_sm me-2">
                                                    <i class="fas fa-trash me-1"></i> Supprimer
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>