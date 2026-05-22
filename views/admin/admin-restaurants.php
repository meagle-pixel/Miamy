<?php
/** @var string $error                 */
/** @var string $success               */
/** @var array  $restaurants           */
/** @var array  $categories_restaurant */

// Valeurs par defaut au cas ou la vue serait appelee sans controller
$error                 = $error                 ?? '';
$success               = $success               ?? '';
$restaurants           = $restaurants           ?? [];
$categories_restaurant = $categories_restaurant ?? [];
?>

<div class="container-fluid px-4">

    <h1 class="mt-4">Gestion des restaurants</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="<?= APP_URL ?>/accueil">Accueil</a></li>
        <li class="breadcrumb-item"><a href="<?= APP_URL ?>/dashboard">Admin</a></li>
        <li class="breadcrumb-item active">Restaurants</li>
    </ol>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if (!empty($success)): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-users me-1"></i>
            Gestion des restaurants
        </div>
        <div class="card-body">
            <?php if (!empty($restaurants)): ?>
                <table id="datatablesSimple" class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID restaurant</th>
                            <th>Nom restaurant</th>
                            <th>Appartient à</th>
                            <th>Email</th>
                            <th>Ville</th>
                            <th>Catégories</th>
                            <th>Statut</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($restaurants as $r): ?>
                            <tr>
                                <td><?= (int) $r['id_restaurant'] ?></td>
                                <td><?= htmlspecialchars($r['name'] ?? '') ?></td>
                                <td><?= htmlspecialchars($r['proprietaire_nom'] . ' ' . $r['proprietaire_prenom'] ?? '') ?></td>
                                <td><?= htmlspecialchars($r['proprietaire_email'] ?? '') ?></td>
                                <td><?= htmlspecialchars($r['city'] ?? '-') ?></td>
                                <td>
                                    <form action="" method="POST" class="d-inline-flex align-items-center gap-2">
                                        <input type="hidden" name="action" value="update_category">
                                        <input type="hidden" name="id_restaurant" value="<?= (int) $r['id_restaurant'] ?>">
                                        <select name="category_id" class="form-select form-select-sm" style="width:auto;">
                                            <option value="0">— Aucune —</option>
                                            <?php foreach ($categories_restaurant as $cat): ?>
                                                <option value="<?= (int) $cat['id_categorie'] ?>"
                                                    <?= ((int)($r['current_category_id'] ?? 0) === (int) $cat['id_categorie']) ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($cat['name']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <button type="submit" class="btn btn-primary btn-sm">Modifier</button>
                                    </form>
                                </td>
                                <td>
                                    <?php if ($r['subscription_active']): ?>
                                        <span class="badge bg-success">Actif</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Inactif</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <form action="" method="POST" onsubmit="return confirm('Supprimer ce restaurant ?')" class="d-inline">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id_restaurant" value="<?= (int) $r['id_restaurant'] ?>">
                                        <button type="submit" class="btn btn-danger btn-sm">
                                            <i class="fas fa-trash"></i> Supprimer
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="text-muted text-center py-4 mb-0">
                    Aucun restaurant enregistré pour le moment.
                </p>
            <?php endif; ?>
        </div>
    </div>

</div>
