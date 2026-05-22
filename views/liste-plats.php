<?php
/** @var array $resto             Restaurant courant */
/** @var array $plats             Plats du restau (déjà filtrés disponibles=1) */
/** @var array $platsParCategorie Plats groupés par catégorie */
/** @var int   $id_restaurant     */

// Valeurs par defaut au cas ou la vue serait appelee sans controller
$resto             = $resto             ?? [];
$plats             = $plats             ?? [];
$platsParCategorie = $platsParCategorie ?? [];
$id_restaurant     = $id_restaurant     ?? 0;
$nbPlats           = count($plats);
?>

<!-- Banner -->
<section id="common_banner">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="common_bannner_text">
                    <h2><?= htmlspecialchars($resto['name'] ?? 'Restaurant') ?></h2>
                    <ul>
                        <li><a href="accueil">Accueil</a></li>
                        <li><span><i class="fas fa-circle"></i></span><a href="liste-restaurants">Restaurants</a></li>
                        <li><span><i class="fas fa-circle"></i></span>Carte</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Carte du restaurant -->
<section class="section_padding">
    <div class="container">

        <!-- Entête resto : image + ville + description -->
        <div class="row mb-5">
            <div class="col-lg-4 col-md-5 mb-3">
                <div class="theme_two_box_img">
                    <img src="<?= APP_URL ?>/assets/img/restaurants/<?= htmlspecialchars($resto['main_image'] ?? 'default-resto.jpg') ?>"
                         alt="<?= htmlspecialchars($resto['name'] ?? '') ?>"
                         style="width:100%; height:240px; object-fit:cover; border-radius:8px;">
                </div>
            </div>
            <div class="col-lg-8 col-md-7">
                <h3 class="mb-2"><?= htmlspecialchars($resto['name'] ?? '') ?></h3>
                <?php if (!empty($resto['city'])): ?>
                    <p class="text-muted mb-2">
                        <i class="fas fa-map-marker-alt me-2"></i><?= htmlspecialchars($resto['city']) ?>
                    </p>
                <?php endif; ?>
                <?php if (!empty($resto['description'])): ?>
                    <p><?= nl2br(htmlspecialchars($resto['description'])) ?></p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Titre + compteur -->
        <div class="row mb-4">
            <div class="col-lg-12">
                <div class="section_heading_center">
                    <h2><?= $nbPlats ?> plat<?= $nbPlats > 1 ? 's' : '' ?> disponible<?= $nbPlats > 1 ? 's' : '' ?></h2>
                </div>
            </div>
        </div>

        <?php if (empty($plats)): ?>
            <div class="alert alert-info text-center py-5 shadow-sm">
                <i class="fas fa-utensils fa-3x mb-3 text-muted"></i>
                <h4>Aucun plat disponible pour le moment.</h4>
                <p>Le restaurateur n'a pas encore mis sa carte en ligne, repassez bientôt !</p>
                <a href="liste-restaurants" class="btn btn_theme mt-2">
                    <i class="fas fa-arrow-left me-2"></i> Retour aux restaurants
                </a>
            </div>
        <?php else: ?>

            <!-- Une section par catégorie -->
            <?php foreach ($platsParCategorie as $categorie => $platsCateg): ?>
                <div class="mb-5">
                    <h3 class="mb-3" style="border-bottom: 2px solid #e74c3c; padding-bottom: 8px;">
                        <?= htmlspecialchars($categorie) ?>
                    </h3>
                    <div class="row">
                        <?php foreach ($platsCateg as $plat): ?>
                            <div class="col-lg-4 col-md-6 col-sm-6 col-12 mb-4">
                                <div class="theme_common_box_two img_hover">
                                    <div class="theme_two_box_img">
                                        <img src="<?= APP_URL ?>/assets/img/plats/<?= !empty($plat['image']) ? htmlspecialchars($plat['image']) : 'default-plat.jpg' ?>"
                                             alt="<?= htmlspecialchars($plat['nom']) ?>"
                                             style="width:100%; height:200px; object-fit:cover;">
                                    </div>
                                    <div class="theme_two_box_content">
                                        <h4><?= htmlspecialchars($plat['nom']) ?></h4>
                                        <?php if (!empty($plat['description'])): ?>
                                            <p class="text-muted" style="font-size:13px; min-height:40px;">
                                                <?= htmlspecialchars(mb_strimwidth($plat['description'], 0, 90, '…')) ?>
                                            </p>
                                        <?php else: ?>
                                            <p style="min-height:40px;">&nbsp;</p>
                                        <?php endif; ?>
                                        <h3><?= number_format($plat['prix'], 2, ',', ' ') ?> €
                                            <span>
                                                <a href="#" class="btn btn_theme btn_sm cart_btn">
                                                    Commander
                                                </a>
                                            </span>
                                        </h3>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>

        <?php endif; ?>

    </div>
</section>
