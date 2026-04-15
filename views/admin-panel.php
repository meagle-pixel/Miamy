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

        if (in_array($new_profil, [2, 3])) {
            try {
                $stmt = $pdo->prepare("UPDATE utilisateurs SET profil = :profil WHERE id = :id AND profil != 1");
                $stmt->execute([':profil' => $new_profil, ':id' => $user_id]);

                logUserAction($_SESSION['user']['id'], 'update_role', "Changement de rôle de l'utilisateur ID $user_id vers profil $new_profil");

                header('Location: ' . $GLOBALS['url'] . '/admin-panel');
                exit();
            } catch (PDOException $e) {
                $error = "Erreur lors de la modification du rôle.";
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

<section id="common_banner">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="common_bannner_text">
                    <h2>Panel Administrateur</h2>
                    <ul>
                        <li><a href="accueil">Accueil</a></li>
                        <li><span><i class="fas fa-circle"></i></span>Admin</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section_padding">
    <div class="container">

        <?php if (isset($error)): ?>
            <p class="error"><?= $error ?></p>
        <?php endif; ?>

        <div class="dashboard_common_table">
            <h3>Gestion des utilisateurs</h3>
            <div class="table_common_area">
                <?php if (!empty($users)): ?>
                    <table class="table table-responsive">
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
                                    <td><?= $user['id'] ?></td>
                                    <td><?= htmlspecialchars($user['nom'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($user['prenom'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($user['email']) ?></td>
                                    <td>
                                        <form action="" method="POST" style="display:inline-flex; gap:8px; align-items:center;">
                                            <input type="hidden" name="action" value="update">
                                            <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                            <select name="profil">
                                                <option value="2" <?= $user['profil'] == 2 ? 'selected' : '' ?>>Restaurateur</option>
                                                <option value="3" <?= $user['profil'] == 3 ? 'selected' : '' ?>>Client</option>
                                            </select>
                                            <button type="submit" class="btn btn_theme btn_sm">Modifier</button>
                                        </form>
                                    </td>
                                    <td>
                                        <form action="" method="POST" onsubmit="return confirm('Supprimer cet utilisateur ?')">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                            <input type="hidden" name="profil_id" value="<?= $user['profil_id'] ?>">
                                            <input type="hidden" name="profil" value="<?= $user['profil'] ?>">
                                            <button type="submit" class="btn btn-danger btn_sm">Supprimer</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <table class="table table-responsive">
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
                            <tr>
                                <td colspan="6">Aucun utilisateur enregistré pour le moment.</td>
                            </tr>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>

    </div>
</section>