<?php

if (!isset($_SESSION['connected']) || $_SESSION['connected'] !== true || $_SESSION['user']['profil'] > 1) {
    header('Location: ' . $GLOBALS['url'] . '/connexion');
    exit();
}

$pdo = Database::getInstance()->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {

    if ($_POST['action'] === 'update' && isset($_POST['user_id'], $_POST['profil'])) {
        $user_id    = (int) $_POST['user_id'];
        $new_profil = (int) $_POST['profil'];

        // 2 = restaurateur, 3 = client (la promotion en admin se fait via un autre flux)
        if (in_array($new_profil, [2, 3], true)) {

            // Si on bascule vers `clients`, les colonnes NOT NULL (civilite, adresse_comp, codepostal, ville)
            // doivent être fournies. On met des valeurs par défaut neutres, à compléter ensuite par l'utilisateur.
            $extraData = [];
            if ($new_profil === 3) {
                $extraData = [
                    'civilite'     => 1,
                    'adresse_comp' => '',
                    'codepostal'   => '',
                    'ville'        => '',
                ];
            }

            if (changeUserProfile($user_id, $new_profil, $extraData)) {
                header('Location: ' . $GLOBALS['url'] . '/admin-panel');
                exit();
            } else {
                $error = "Erreur lors de la modification du rôle (voir error_log).";
            }
        }
    }

    if ($_POST['action'] === 'delete' && isset($_POST['user_id'], $_POST['profil_id'], $_POST['profil'])) {
        $profil_id = (int) $_POST['profil_id'];
        $profil    = (int) $_POST['profil'];

        try {
            deleteUser($profil_id, $profil);

            header('Location: ' . $GLOBALS['url'] . '/admin-panel');
            exit();
        } catch (PDOException $e) {
            $error = "Erreur lors de la suppression.";
        }
    }
}

try {
    $stmt = $pdo->prepare("
        SELECT 
            u.*,
            COALESCE(r.nom, c.nom) AS nom,
            COALESCE(r.prenom, c.prenom) AS prenom,
            COALESCE(r.telephone, c.telephone) AS telephone
        FROM utilisateurs u
        LEFT JOIN restaurateurs r ON (u.profil = 2 AND u.profil_id = r.id)
        LEFT JOIN clients c ON (u.profil = 3 AND u.profil_id = c.id)
        WHERE u.profil IN (2, 3)
        ORDER BY u.id ASC
    ");
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Erreur lors de la récupération des utilisateurs.";
}

?>

<div class="container-fluid px-4">

    <h1 class="mt-4">Panel Administrateur</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="<?= $GLOBALS['url'] ?>/accueil">Accueil</a></li>
        <li class="breadcrumb-item"><a href="<?= $GLOBALS['url'] ?>/dashboard">Admin</a></li>
        <li class="breadcrumb-item active">Utilisateurs</li>
    </ol>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-users me-1"></i>
            Gestion des utilisateurs
        </div>
        <div class="card-body">
            <?php if (!empty($users)): ?>
                <table id="datatablesSimple" class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nom</th>
                            <th>Prénom</th>
                            <th>Email</th>
                            <th>Rôle</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?= (int) $user['id'] ?></td>
                                <td><?= htmlspecialchars($user['nom'] ?? '') ?></td>
                                <td><?= htmlspecialchars($user['prenom'] ?? '') ?></td>
                                <td><?= htmlspecialchars($user['email']) ?></td>
                                <td>
                                    <form action="" method="POST" class="d-inline-flex align-items-center gap-2">
                                        <input type="hidden" name="action" value="update">
                                        <input type="hidden" name="user_id" value="<?= (int) $user['id'] ?>">
                                        <select name="profil" class="form-select form-select-sm" style="width:auto;">
                                            <option value="2" <?= $user['profil'] == 2 ? 'selected' : '' ?>>Restaurateur</option>
                                            <option value="3" <?= $user['profil'] == 3 ? 'selected' : '' ?>>Client</option>
                                        </select>
                                        <button type="submit" class="btn btn-primary btn-sm">Modifier</button>
                                    </form>
                                </td>
                                <td>
                                    <form action="" method="POST" onsubmit="return confirm('Supprimer cet utilisateur ?')" class="d-inline">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="user_id" value="<?= (int) $user['id'] ?>">
                                        <input type="hidden" name="profil_id" value="<?= (int) $user['profil_id'] ?>">
                                        <input type="hidden" name="profil" value="<?= (int) $user['profil'] ?>">
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
                    Aucun utilisateur enregistré pour le moment.
                </p>
            <?php endif; ?>
        </div>
    </div>

</div>