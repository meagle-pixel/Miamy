<?php
// 1. Sécurité
if (!isset($_SESSION['connected']) || $_SESSION['connected'] !== true || $_SESSION['user']['profil'] > 2) {
    header('Location: ' . $GLOBALS['url'] . '/connexion');
    exit();
}

// 2. Récupération et validation de l'ID restaurant
$id_restaurant = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$id_restaurant) {
    header('Location: ' . $GLOBALS['url'] . '/mon-compte-restaurateur');
    exit();
}

// 3. Je vérifie que le restaurant appartient bien au restaurateur connecté
$id_restaurateur = $_SESSION['user']['profil_id'];
$pdo  = Database::getInstance()->getConnection();
$stmt = $pdo->prepare("SELECT * FROM restaurants WHERE id_restaurant = :id AND id_restaurateur = :id_proprio");
$stmt->execute(['id' => $id_restaurant, 'id_proprio' => $id_restaurateur]);
$resto = $stmt->fetch();

if (!$resto) {
    header('Location: ' . $GLOBALS['url'] . '/mon-compte-restaurateur');
    exit();
}

// 4. Récupération des plats
$platClass = new Plat();
$plats     = $platClass->getByRestaurant($id_restaurant);

// 5. Calcul des stats
$totalPlats        = count($plats);
$platsDisponibles  = count(array_filter($plats, fn($p) => $p['disponible']));
$platsIndisponibles = $totalPlats - $platsDisponibles;

if ($totalPlats > 0) {
    $prixMoyen = array_sum(array_column($plats, 'prix')) / $totalPlats;
} else {
    $prixMoyen = 0;
}

// 6. Récupération des 3 derniers plats
$dernierPlats = $platClass->getDerniersPlats($id_restaurant);

// 7. Récupération des horaires
$horairesClass = new Horaires();
$horaires      = $horairesClass->getByRestaurant($id_restaurant);

// 8. Message horaires
$horaires_success = isset($_GET['horaires']) && $_GET['horaires'] === 'ok';
$horaires_error   = isset($_GET['horaires']) && $_GET['horaires'] === 'error';


?>

<section id="common_banner">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="common_bannner_text">
                    <h2>Détails — <?= htmlspecialchars($resto['name']) ?></h2>
                    <ul>
                        <li><a href="mon-compte-restaurateur">Tableau de bord</a></li>
                        <li><span><i class="fas fa-circle"></i></span>Détails</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<section id="dashboard_main_arae" class="section_padding">
    <div class="container">

        <!-- Retour -->
        <a href="mon-compte-restaurateur" class="btn btn_theme btn_sm mb-4">
            <i class="fas fa-arrow-left me-2"></i> Retour
        </a>

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

            <!-- Stats -->
            <div class="row mb-5">
                <div class="col-md-3 mb-3">
                    <div class="bg-white shadow-sm rounded p-4 text-center border">
                        <h2 class="text-primary mb-0"><?= $totalPlats ?></h2>
                        <small class="text-muted">Plat<?= $totalPlats > 1 ? 's' : '' ?> au total</small>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="bg-white shadow-sm rounded p-4 text-center border">
                        <h2 class="text-success mb-0"><?= $platsDisponibles ?></h2>
                        <small class="text-muted">Disponible<?= $platsDisponibles > 1 ? 's' : '' ?></small>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="bg-white shadow-sm rounded p-4 text-center border">
                        <h2 class="text-danger mb-0"><?= $platsIndisponibles ?></h2>
                        <small class="text-muted">Indisponible<?= $platsIndisponibles > 1 ? 's' : '' ?></small>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="bg-white shadow-sm rounded p-4 text-center border">
                        <h2 class="text-info mb-0"><?= number_format(($prixMoyen), 2) . " €" ?></h2>
                        <small class="text-muted">Prix moyen de la carte </small>
                    </div>
                </div>
            </div>

            <!-- 3 derniers plats ajoutés -->
            <div class="section_heading mb-4">
                <h2>3 derniers plats ajoutés</h2>
            </div>
            <div class="row">
                <?php foreach ($dernierPlats as $plat): ?>
                    <div class="col-md-4 mb-4">
                        <div class="bg-white shadow-sm rounded border overflow-hidden">
                            <img src="<?= $GLOBALS['url'] ?>/assets/img/plats/<?= !empty($plat['image']) ? htmlspecialchars($plat['image']) : 'default-plat.jpg' ?>"
                                alt="<?= htmlspecialchars($plat['nom']) ?>"
                                style="width:100%; height:180px; object-fit:cover;">
                            <div class="p-3">
                                <h5 class="mb-1"><?= htmlspecialchars($plat['nom']) ?></h5>
                                <span class="badge bg-secondary mb-2"><?= htmlspecialchars($plat['categorie']) ?></span>
                                <p class="text-primary fw-bold mb-0"><?= number_format($plat['prix'], 2) ?> €</p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

        <?php endif; ?>

        <!-- Horaires d'ouverture -->
        <div class="section_heading mb-4 mt-5">
            <h2>Horaires d'ouverture</h2>
        </div>

        <?php if ($horaires_success): ?>
            <div class="alert alert-success shadow-sm mb-4">
                <i class="fas fa-check-circle me-2"></i> Horaires mis à jour avec succès !
            </div>
        <?php elseif ($horaires_error): ?>
            <div class="alert alert-danger shadow-sm mb-4">
                <i class="fas fa-times-circle me-2"></i> Une erreur est survenue. Veuillez réessayer.
            </div>
        <?php endif; ?>

        <form method="POST" action="<?= $GLOBALS['url'] ?>/actions/save-horaires.php">
            <input type="hidden" name="id_restaurant" value="<?= $id_restaurant ?>">

            <div class="bg-white shadow-sm rounded border p-4">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Jour</th>
                                <th class="text-center">Ouvert</th>
                                <th>Ouverture</th>
                                <th>Fermeture</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach (Horaires::$jours as $num => $label): ?>
                                <?php $h = $horaires[$num]; ?>
                                <tr class="horaire-row" data-jour="<?= $num ?>">
                                    <td class="fw-semibold"><?= $label ?></td>
                                    <td class="text-center">
                                        <div class="form-check form-switch d-flex justify-content-center">
                                            <input class="form-check-input toggle-ouvert" type="checkbox"
                                                name="horaires[<?= $num ?>][ouvert]"
                                                id="ouvert_<?= $num ?>"
                                                <?= $h['ouvert'] ? 'checked' : '' ?>>
                                        </div>
                                    </td>
                                    <td>
                                        <input type="time" class="form-control form-control-sm heures-input"
                                            name="horaires[<?= $num ?>][debut]"
                                            value="<?= $h['debut'] ? substr($h['debut'], 0, 5) : '09:00' ?>"
                                            <?= !$h['ouvert'] ? 'disabled' : '' ?>
                                            style="max-width:130px;">
                                    </td>
                                    <td>
                                        <input type="time" class="form-control form-control-sm heures-input"
                                            name="horaires[<?= $num ?>][fin]"
                                            value="<?= $h['fin'] ? substr($h['fin'], 0, 5) : '22:00' ?>"
                                            <?= !$h['ouvert'] ? 'disabled' : '' ?>
                                            style="max-width:130px;">
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="text-end mt-3">
                    <button type="submit" class="btn btn_theme btn_md">
                        <i class="fas fa-save me-2"></i> Enregistrer les horaires
                    </button>
                </div>
            </div>
        </form>

        <script>
        document.querySelectorAll('.toggle-ouvert').forEach(function (checkbox) {
            checkbox.addEventListener('change', function () {
                const row    = this.closest('.horaire-row');
                const inputs = row.querySelectorAll('.heures-input');
                inputs.forEach(function (input) {
                    input.disabled = !checkbox.checked;
                });
            });
        });
        </script>

    </div>
</section>