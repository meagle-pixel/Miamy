<?php
/** @var array $restaurants        */
/** @var array $horairesAujourdhui */
?>

<!-- Common Banner Area -->
<section id="common_banner">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="common_bannner_text">
                    <h2>Restaurants</h2>
                    <ul>
                        <li><a href="accueil">Accueil</a></li>
                        <li><span><i class="fas fa-circle"></i></span>Restaurants</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Liste des restaurants -->
<section class="section_padding">
    <div class="container">

        <div class="row mb-4">
            <div class="col-lg-12 col-md-12 col-sm-12 col-12">
                <div class="section_heading_center">
                    <h2>Nos restaurants</h2>
                    <p><?= count($restaurants) ?> restaurant<?= count($restaurants) > 1 ? 's' : '' ?> disponible<?= count($restaurants) > 1 ? 's' : '' ?></p>
                </div>
            </div>
        </div>

        <?php if (empty($restaurants)): ?>
            <div class="alert alert-info text-center py-5">
                <i class="fas fa-store-slash fa-3x mb-3 text-muted"></i>
                <h4>Aucun restaurant disponible pour le moment.</h4>
            </div>
        <?php else: ?>
            <div class="row">
                <?php foreach ($restaurants as $resto):
                    $image   = !empty($resto['main_image']) ? $resto['main_image'] : 'default-resto.jpg';
                    $horaire = $horairesAujourdhui[$resto['id_restaurant']] ?? null;

                    // Calcul statut ouverture
                    if ($horaire === null) {
                        $statutHtml = '';
                    } elseif (!$horaire['ouvert']) {
                        $statutHtml = '<span class="badge bg-danger"><i class="fas fa-clock me-1"></i>Fermé aujourd\'hui</span>';
                    } else {
                        $debut      = substr($horaire['debut'], 0, 5);
                        $fin        = substr($horaire['fin'],   0, 5);
                        $statutHtml = '<span class="badge bg-success"><i class="fas fa-clock me-1"></i>'
                                    . 'Ouvert · ' . $debut . ' – ' . $fin
                                    . '</span>';
                    }
                ?>
                    <div class="col-lg-4 col-md-6 col-sm-6 col-12 mb-4">
                        <div class="theme_common_box_two img_hover">
                            <div class="theme_two_box_img">
                                <a href="<?= $GLOBALS['url'] ?>/liste-plats?id=<?= $resto['id_restaurant'] ?>">
                                    <img src="<?= $GLOBALS['url'] ?>/assets/img/restaurants/<?= htmlspecialchars($image) ?>"
                                         alt="<?= htmlspecialchars($resto['name']) ?>">
                                </a>
                                <?php if ($resto['is_featured']): ?>
                                    <div class="discount_tab">
                                        <span><i class="fas fa-star"></i> Coup de cœur</span>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="theme_two_box_content">
                                <h4>
                                    <a href="<?= $GLOBALS['url'] ?>/liste-plats?id=<?= $resto['id_restaurant'] ?>">
                                        <?= htmlspecialchars($resto['name']) ?>
                                    </a>
                                </h4>
                                <?php if ($statutHtml): ?>
                                    <p class="mb-1"><?= $statutHtml ?></p>
                                <?php endif; ?>
                                <h3>
                                    <i class="fas fa-map-marker-alt me-1 text-muted" style="font-size:.85rem;"></i>
                                    <?= htmlspecialchars($resto['city'] ?? '') ?>
                                    <span>
                                        <a href="<?= $GLOBALS['url'] ?>/liste-plats?id=<?= $resto['id_restaurant'] ?>"
                                           class="btn btn_theme btn_sm cart_btn">Voir</a>
                                    </span>
                                </h3>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </div>
</section>
