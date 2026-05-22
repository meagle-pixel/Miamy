<?php
/** @var string $error */
/** @var array  $users */

// Valeurs par defaut au cas ou la vue serait appelee sans controller
$error = $error ?? '';
$users = $users ?? [];
?>

<div class="container-fluid px-4">

    <h1 class="mt-4">Panel Administrateur</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="<?= APP_URL ?>/accueil">Accueil</a></li>
        <li class="breadcrumb-item"><a href="<?= APP_URL ?>/dashboard">Admin</a></li>
        <li class="breadcrumb-item active">Utilisateurs</li>
    </ol>

    <?php if (!empty($error)): ?>
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
