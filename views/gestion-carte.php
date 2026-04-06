<?php
// 1. Sécurité
if (!isset($_SESSION['connected']) || $_SESSION['connected'] !== true || $_SESSION['user']['profil'] > 2) {
    echo "<script>window.location.href='" . $GLOBALS['url'] . "/connexion';</script>";
    exit();
}

$id_restaurant   = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$id_restaurateur = $_SESSION['user']['profil_id'];

if (!$id_restaurant) {
    echo "<script>window.location.href='" . $GLOBALS['url'] . "/mon-compte-restaurateur';</script>";
    exit();
}

// 2. Vérifier que le restaurant appartient bien à ce restaurateur
$pdo  = Database::getInstance()->getConnection();
$stmt = $pdo->prepare("SELECT * FROM restaurants WHERE id = ? AND id_restaurateur = ?");
$stmt->execute([$id_restaurant, $id_restaurateur]);
$resto = $stmt->fetch();

if (!$resto) {
    echo "<script>window.location.href='" . $GLOBALS['url'] . "/mon-compte-restaurateur';</script>";
    exit();
}

// 3. Récupérer les plats
$platClass = new Plat();
$plats     = $platClass->getByRestaurant($id_restaurant);

// Regrouper les plats par catégorie
$platsParCategorie = [];
foreach ($plats as $plat) {
    $platsParCategorie[$plat['categorie']][] = $plat;
}

// 4. Messages
$message_success = '';
$message_error   = '';
if (isset($_GET['success'])) {
    $msgs = [
        'added'   => 'Plat ajouté avec succès !',
        'updated' => 'Plat modifié avec succès !',
        'deleted' => 'Plat supprimé avec succès.',
    ];
    $message_success = $msgs[$_GET['success']] ?? '';
}
?>

<section id="common_banner">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="common_bannner_text">
                    <h2>Gestion de la carte</h2>
                    <ul>
                        <li><a href="accueil">Accueil</a></li>
                        <li><span><i class="fas fa-circle"></i></span><a href="mon-compte-restaurateur">Mon compte</a></li>
                        <li><span><i class="fas fa-circle"></i></span><?= htmlspecialchars($resto['name']) ?></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<section id="dashboard_main_arae" class="section_padding">
    <div class="container">

        <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <img src="<?= $GLOBALS['url'] ?>/assets/img/restaurants/<?= !empty($resto['main_image']) ? $resto['main_image'] : 'default-resto.jpg' ?>"
                     alt="img" class="rounded shadow-sm" style="width:70px; height:50px; object-fit:cover;">
                <div>
                    <h3 class="mb-0"><?= htmlspecialchars($resto['name']) ?></h3>
                    <small class="text-muted"><i class="fas fa-map-marker-alt me-1"></i><?= htmlspecialchars($resto['city']) ?></small>
                </div>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <a href="ajouter-plat?id_restaurant=<?= $id_restaurant ?>" class="btn btn_theme btn_md">
                    <i class="fas fa-plus me-2"></i> Ajouter un plat
                </a>
                <a href="mon-compte-restaurateur" class="btn btn-outline-secondary btn_md">
                    <i class="fas fa-arrow-left me-2"></i> Retour
                </a>
            </div>
        </div>

        <?php if ($message_success): ?>
            <div class="alert alert-success shadow-sm">
                <i class="fas fa-check-circle me-2"></i> <?= $message_success ?>
            </div>
        <?php endif; ?>
        <?php if ($message_error): ?>
            <div class="alert alert-danger shadow-sm">
                <i class="fas fa-times-circle me-2"></i> <?= $message_error ?>
            </div>
        <?php endif; ?>

        <?php if (empty($plats)): ?>
            <div class="alert alert-info text-center py-5 shadow-sm">
                <i class="fas fa-book-open fa-3x mb-3 text-muted"></i>
                <h4>Votre carte est vide pour l'instant.</h4>
                <p>Ajoutez vos premiers plats pour que vos clients puissent commander.</p>
                <a href="ajouter-plat?id_restaurant=<?= $id_restaurant ?>" class="btn btn_theme mt-2">
                    <i class="fas fa-plus me-2"></i> Ajouter un plat
                </a>
            </div>
        <?php else: ?>
            <div class="row mb-4">
                <div class="col-md-4 mb-3">
                    <div class="bg-white shadow-sm rounded p-3 text-center border">
                        <h2 class="text-primary mb-0"><?= count($plats) ?></h2>
                        <small class="text-muted">Plat<?= count($plats) > 1 ? 's' : '' ?> au total</small>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="bg-white shadow-sm rounded p-3 text-center border">
                        <h2 class="text-success mb-0"><?= count(array_filter($plats, fn($p) => $p['disponible'])) ?></h2>
                        <small class="text-muted">Disponible<?= count(array_filter($plats, fn($p) => $p['disponible'])) > 1 ? 's' : '' ?></small>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="bg-white shadow-sm rounded p-3 text-center border">
                        <h2 class="text-warning mb-0"><?= count($platsParCategorie) ?></h2>
                        <small class="text-muted">Catégorie<?= count($platsParCategorie) > 1 ? 's' : '' ?></small>
                    </div>
                </div>
            </div>

            <?php foreach ($platsParCategorie as $categorie => $platsCateg): ?>
                <div class="mb-5">
                    <div class="d-flex align-items-center mb-3">
                        <h4 class="mb-0 me-3">
                            <span class="badge bg-secondary fs-6">
                                <i class="fas fa-utensils me-2"></i><?= htmlspecialchars($categorie) ?>
                            </span>
                        </h4>
                        <small class="text-muted"><?= count($platsCateg) ?> plat<?= count($platsCateg) > 1 ? 's' : '' ?></small>
                    </div>

                    <div class="row">
                        <?php foreach ($platsCateg as $plat): ?>
                            <div class="col-lg-12 mb-3">
                                <div class="d-md-flex align-items-center bg-white shadow-sm rounded overflow-hidden border p-3 gap-3">

                                    <div class="flex-shrink-0 mb-3 mb-md-0">
                                        <?php if (!empty($plat['image'])): ?>
                                            <img src="<?= $GLOBALS['url'] ?>/assets/img/plats/<?= htmlspecialchars($plat['image']) ?>"
                                                 alt="<?= htmlspecialchars($plat['nom']) ?>"
                                                 class="rounded" style="width:110px; height:80px; object-fit:cover;">
                                        <?php else: ?>
                                            <div class="rounded bg-light d-flex align-items-center justify-content-center"
                                                 style="width:110px; height:80px;">
                                                <i class="fas fa-utensils fa-2x text-muted"></i>
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                                            <div>
                                                <h5 class="mb-1">
                                                    <?= htmlspecialchars($plat['nom']) ?>
                                                    <?php if (!$plat['disponible']): ?>
                                                        <span class="badge bg-danger ms-2" style="font-size:.7rem;">Indisponible</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-success ms-2" style="font-size:.7rem;">Disponible</span>
                                                    <?php endif; ?>
                                                </h5>
                                                <?php if (!empty($plat['description'])): ?>
                                                    <p class="text-muted mb-1" style="font-size:.9rem; max-width:500px;">
                                                        <?= htmlspecialchars(mb_substr($plat['description'], 0, 120)) ?><?= mb_strlen($plat['description']) > 120 ? '…' : '' ?>
                                                    </p>
                                                <?php endif; ?>
                                                <strong class="text-primary"><?= number_format($plat['prix'], 2, ',', ' ') ?> €</strong>
                                            </div>

                                            <div class="d-flex gap-2 flex-wrap align-items-center">
                                                <a href="modifier-plat?id=<?= $plat['id'] ?>"
                                                   class="btn btn-outline-secondary btn_sm" title="Modifier">
                                                    <i class="fas fa-edit me-1"></i> Modifier
                                                </a>
                                                <a href="supprimer-plat?id=<?= $plat['id'] ?>&id_restaurant=<?= $id_restaurant ?>"
                                                   class="btn btn-outline-danger btn_sm" title="Supprimer">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </div>
                                        </div>
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
