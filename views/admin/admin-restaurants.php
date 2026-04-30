<?php
if (!isset($_SESSION['connected']) || $_SESSION['connected'] !== true || $_SESSION['user']['profil'] > 1) {
    header('Location: ' . $GLOBALS['url'] . '/connexion');
    exit();
}

$error   = '';
$success = '';

// Traitement de la suppression d'un restaurant (action admin)
if (
    $_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['action'], $_POST['id_restaurant'])
    && $_POST['action'] === 'delete'
) {

    $idToDelete = (int) $_POST['id_restaurant'];

    try {
        $resto = new Restaurant();
        if ($resto->delete($idToDelete)) {
            header('Location: ' . $GLOBALS['url'] . '/admin-restaurants?success=deleted');
            exit();
        } else {
            $error = "La suppression du restaurant a échoué.";
        }
    } catch (PDOException $e) {
        error_log('[admin-restaurants] delete : ' . $e->getMessage());
        $error = "Erreur lors de la suppression. Détail technique : " . $e->getMessage();
    }
}

// Traitement de la modification de catégorie d'un restaurant
if (
    $_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['action'], $_POST['id_restaurant'], $_POST['category_id'])
    && $_POST['action'] === 'update_category'
) {
    $idRestaurant = (int) $_POST['id_restaurant'];
    $idCategorie  = (int) $_POST['category_id'];

    try {
        $resto = new Restaurant();
        if ($resto->updateCategory($idRestaurant, $idCategorie)) {
            header('Location: ' . $GLOBALS['url'] . '/admin-restaurants?success=updated');
            exit();
        } else {
            $error = "La mise à jour de la catégorie a échoué.";
        }
    } catch (PDOException $e) {
        error_log('[admin-restaurants] update_category : ' . $e->getMessage());
        $error = "Erreur lors de la modification de la catégorie. Détail technique : " . $e->getMessage();
    }
}

if (isset($_GET['success'])) {
    if ($_GET['success'] === 'deleted') {
        $success = "Restaurant supprimé avec succès.";
    } elseif ($_GET['success'] === 'updated') {
        $success = "Catégorie mise à jour avec succès.";
    }
}

try {
    $resto = new Restaurant();
    $restaurants = $resto->listRestaurants(false);
    $categories_restaurant = (new Category())->listAll();
} catch (PDOException $e) {
    $error = "Erreur lors du chargement des données.";
    error_log('[admin-restaurants] load : ' . $e->getMessage());
}

?>

<div class="container-fluid px-4">

    <h1 class="mt-4">Gestion des restaurants</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="<?= $GLOBALS['url'] ?>/accueil">Accueil</a></li>
        <li class="breadcrumb-item"><a href="<?= $GLOBALS['url'] ?>/dashboard">Admin</a></li>
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