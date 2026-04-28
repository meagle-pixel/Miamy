<?php

if (!isset($_SESSION['connected']) || $_SESSION['connected'] !== true || $_SESSION['user']['profil'] > 1) {
    header('Location: ' . $GLOBALS['url'] . '/connexion');
    exit();
}

// $pdo = Database::getInstance()->getConnection(); ---> Plus besoin car la fonction listRestaurants le fait déjà.


try {
    $resto = new Restaurant();
    $restaurants = $resto->listRestaurants(false);
} catch (PDOException $e) {
    $error = "Erreur lors de la récupération des restaurants.";
}


?>

<div class="container-fluid px-4">

    <h1 class="mt-4">Gestion des restaurants</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="<?= $GLOBALS['url'] ?>/accueil">Accueil</a></li>
        <li class="breadcrumb-item"><a href="<?= $GLOBALS['url'] ?>/dashboard">Admin</a></li>
        <li class="breadcrumb-item active">Restaurants</li>
    </ol>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
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
                            <th>ID</th>
                            <th>Nom</th>
                            <th>Ville</th>
                            <th>Catégories</th>
                            <th>Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($restaurants as $r): ?>
                            <tr>
                                <td><?= (int) $r['id_restaurant'] ?></td>
                                <td><?= htmlspecialchars($r['name'] ?? '') ?></td>
                                <td><?= htmlspecialchars($r['city'] ?? '') ?? '-' ?></td>
                                <td><?= htmlspecialchars($r['category_name']) ?? '-' ?></td>
                                <td>
                                    <?php if ($r['subscription_active']): ?>
                                        <span class="badge bg-success">Actif</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Inactif</span>
                                    <?php endif; ?>
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