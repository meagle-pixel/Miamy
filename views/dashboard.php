<?php

// TABLEAU DE BORD ADMIN

if (!isset($_SESSION['connected']) || $_SESSION['connected'] !== true || $_SESSION['user']['profil'] > 1) {
    header('Location: ' . $GLOBALS['url'] . '/connexion');
    exit();
}

$pdo = Database::getInstance()->getConnection();

// Nombre total de restaurants 
$nb_restaurants_total = 0;

try {
    $stmt = $pdo->query("SELECT COUNT(*) FROM restaurants");
    $nb_restaurants_total = (int) $stmt->fetchColumn();
} catch (Exception $e) {
}

// Nombre de restaurants actifs (abonnement actif)$nb_restaurants_actifs = 0;
try {
    $stmt = $pdo->query("SELECT COUNT(*) FROM restaurants WHERE subscription_active = 1");
    $nb_restaurants_actifs = (int) $stmt->fetchColumn();
} catch (Exception $e) {
}

//  Nombre d'utilisateurs inscrits (clients + restaurateurs) $nb_utilisateurs = 0;
try {
    $stmt = $pdo->query("SELECT COUNT(*) FROM utilisateurs WHERE profil IN (2, 3)");
    $nb_utilisateurs = (int) $stmt->fetchColumn();
} catch (Exception $e) {
}
//  Nombre de plats disponibles 
$nb_plats = 0;
try {
    $stmt = $pdo->query("SELECT COUNT(*) FROM plats WHERE disponible = 1");
    $nb_plats = (int) $stmt->fetchColumn();
} catch (Exception $e) {
}

//  Commandes du jour 
$nb_commandes_jour = 0;
$ca_jour = 0;
try {
    $stmt = $pdo->query("SELECT COUNT(*), COALESCE(SUM(totalttc), 0) FROM commandes WHERE DATE(date_commande) = CURDATE()");
    $row = $stmt->fetch(PDO::FETCH_NUM);
    $nb_commandes_jour = (int) $row[0];
    $ca_jour = (float) $row[1];
} catch (Exception $e) {
}

//  Dernières inscriptions (5 derniers utilisateurs) 
$derniers_utilisateurs = [];
try {
    $stmt = $pdo->query("
        SELECT u.email, u.dateinscription,
               CASE u.profil WHEN 2 THEN 'Restaurateur' ELSE 'Client' END AS role
        FROM utilisateurs u
        WHERE u.profil IN (2, 3)
        ORDER BY u.dateinscription DESC
        LIMIT 5
    ");
    $derniers_utilisateurs = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
}

// Derniers restaurants ajoutés
$derniers_restaurants = [];
try {
    $stmt = $pdo->query("
        SELECT name, city, subscription_active, created_at
        FROM restaurants
        ORDER BY created_at DESC
        LIMIT 5
    ");
    $derniers_restaurants = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
}

// Dernières actions dans les log
$derniers_logs = [];
try {
    $stmt = $pdo->query("
        SELECT l.action_type, l.message, l.created_at, u.email
        FROM user_logs l
        LEFT JOIN utilisateurs u ON l.user_id = u.id
        ORDER BY l.created_at DESC
        LIMIT 5
    ");
    $derniers_logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
}

// Codes promo actif
$nb_promos_actives = 0;
try {
    $stmt = $pdo->query("SELECT COUNT(*) FROM promos WHERE actif = 1");
    $nb_promos_actives = (int) $stmt->fetchColumn();
} catch (Exception $e) {
}

?>
<section id="common_banner">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="common_bannner_text">
                    <h2>Tableau de bord</h2>
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

        <!-- Titre de la page -->
        <div class="row mb-4">
            <div class="col-12">
                <h3 class="dashboard_common_title">
                    <i class="fas fa-chart-line me-2"></i>
                    Statistiques globales
                </h3>
                <p class="text-muted">
                    Données en temps réel — <?= date('d/m/Y à H:i') ?>
                </p>
            </div>
        </div>


        <div class="row g-4 mb-4">

            <!-- Carte : Restaurants actifs -->
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #1D9E75 !important;">
                    <div class="card-body d-flex align-items-center gap-3">
                        <!-- Icône -->
                        <div style="width:50px; height:50px; background:#E1F5EE; border-radius:12px;
                                    display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                            <i class="fas fa-store" style="color:#1D9E75; font-size:20px;"></i>
                        </div>
                        <!-- Chiffres -->
                        <div>
                            <div style="font-size:28px; font-weight:700; line-height:1;">
                                <?= $nb_restaurants_actifs ?>
                            </div>
                            <div class="text-muted" style="font-size:13px;">Restaurants actifs</div>
                            <div style="font-size:12px; color:#aaa;"><?= $nb_restaurants_total ?> au total</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Carte : Utilisateurs inscrits -->
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #4361EE !important;">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div style="width:50px; height:50px; background:#EEEDFE; border-radius:12px;
                                    display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                            <i class="fas fa-users" style="color:#4361EE; font-size:20px;"></i>
                        </div>
                        <div>
                            <div style="font-size:28px; font-weight:700; line-height:1;">
                                <?= $nb_utilisateurs ?>
                            </div>
                            <div class="text-muted" style="font-size:13px;">Utilisateurs inscrits</div>
                            <div style="font-size:12px; color:#aaa;">clients &amp; restaurateurs</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Carte : Commandes du jour -->
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #EF9F27 !important;">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div style="width:50px; height:50px; background:#FEF3E1; border-radius:12px;
                                    display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                            <i class="fas fa-receipt" style="color:#EF9F27; font-size:20px;"></i>
                        </div>
                        <div>
                            <div style="font-size:28px; font-weight:700; line-height:1;">
                                <?= $nb_commandes_jour ?>
                            </div>
                            <div class="text-muted" style="font-size:13px;">Commandes aujourd'hui</div>
                            <div style="font-size:12px; color:#aaa;">CA : <?= number_format($ca_jour, 2, ',', ' ') ?> €</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Carte : Plats disponibles -->
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #E24B4A !important;">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div style="width:50px; height:50px; background:#FEEBEB; border-radius:12px;
                                    display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                            <i class="fas fa-utensils" style="color:#E24B4A; font-size:20px;"></i>
                        </div>
                        <div>
                            <div style="font-size:28px; font-weight:700; line-height:1;">
                                <?= $nb_plats ?>
                            </div>
                            <div class="text-muted" style="font-size:13px;">Plats disponibles</div>
                            <div style="font-size:12px; color:#aaa;"><?= $nb_promos_actives ?> code(s) promo actif(s)</div>
                        </div>
                    </div>
                </div>
            </div>

        </div>


        <div class="row g-4">

            <!-- Bloc gauche : Derniers restaurants -->
            <div class="col-12 col-lg-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center py-3">
                        <h5 class="mb-0" style="font-size:15px; font-weight:600;">
                            <i class="fas fa-store me-2" style="color:#1D9E75;"></i>
                            Derniers restaurants ajoutés
                        </h5>
                        <a href="<?= $GLOBALS['url'] ?>/liste-restaurants" class="btn btn-sm btn_theme" style="font-size:12px;">
                            Voir tout
                        </a>
                    </div>
                    <div class="card-body p-0">
                        <?php if (!empty($derniers_restaurants)): ?>
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th style="font-size:13px;">Nom</th>
                                        <th style="font-size:13px;">Ville</th>
                                        <th style="font-size:13px;">Statut</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($derniers_restaurants as $resto): ?>
                                        <tr>
                                            <td style="font-size:13px;"><?= htmlspecialchars($resto['name']) ?></td>
                                            <td style="font-size:13px;"><?= htmlspecialchars($resto['city'] ?? '—') ?></td>
                                            <td>
                                                <?php if ($resto['subscription_active']): ?>
                                                    <!-- Badge vert = actif -->
                                                    <span class="badge" style="background:#E1F5EE; color:#085041; font-size:11px;">Actif</span>
                                                <?php else: ?>
                                                    <!-- Badge gris = inactif -->
                                                    <span class="badge" style="background:#f0f0f0; color:#888; font-size:11px;">Inactif</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <p class="text-muted text-center py-4" style="font-size:14px;">
                                Aucun restaurant pour le moment.
                            </p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Bloc droite : Dernières inscriptions -->
            <div class="col-12 col-lg-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center py-3">
                        <h5 class="mb-0" style="font-size:15px; font-weight:600;">
                            <i class="fas fa-user-plus me-2" style="color:#4361EE;"></i>
                            Dernières inscriptions
                        </h5>
                        <a href="<?= $GLOBALS['url'] ?>/admin-panel" class="btn btn-sm btn_theme" style="font-size:12px;">
                            Voir tout
                        </a>
                    </div>
                    <div class="card-body p-0">
                        <?php if (!empty($derniers_utilisateurs)): ?>
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th style="font-size:13px;">Email</th>
                                        <th style="font-size:13px;">Rôle</th>
                                        <th style="font-size:13px;">Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($derniers_utilisateurs as $user): ?>
                                        <tr>
                                            <td style="font-size:13px;"><?= htmlspecialchars($user['email']) ?></td>
                                            <td style="font-size:13px;">
                                                <?php if ($user['role'] === 'Restaurateur'): ?>
                                                    <span class="badge" style="background:#EEEDFE; color:#3C3489; font-size:11px;">Restaurateur</span>
                                                <?php else: ?>
                                                    <span class="badge" style="background:#E1F5EE; color:#085041; font-size:11px;">Client</span>
                                                <?php endif; ?>
                                            </td>
                                            <td style="font-size:13px; color:#888;">
                                                <!-- Affichage de la date en format français -->
                                                <?= $user['dateinscription']
                                                    ? date('d/m/Y', strtotime($user['dateinscription']))
                                                    : '—' ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <p class="text-muted text-center py-4" style="font-size:14px;">
                                Aucune inscription pour le moment.
                            </p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

        </div>



        <?php if (!empty($derniers_logs)): ?>
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-bottom py-3">
                            <h5 class="mb-0" style="font-size:15px; font-weight:600;">
                                <i class="fas fa-history me-2" style="color:#888;"></i>
                                Dernières activités
                            </h5>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th style="font-size:13px;">Action</th>
                                        <th style="font-size:13px;">Message</th>
                                        <th style="font-size:13px;">Utilisateur</th>
                                        <th style="font-size:13px;">Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($derniers_logs as $log): ?>
                                        <tr>
                                            <!-- Badge de type d'action -->
                                            <td>
                                                <span class="badge" style="background:#f0f0f0; color:#555; font-size:11px;">
                                                    <?= htmlspecialchars($log['action_type']) ?>
                                                </span>
                                            </td>
                                            <td style="font-size:13px;"><?= htmlspecialchars($log['message']) ?></td>
                                            <td style="font-size:13px; color:#888;"><?= htmlspecialchars($log['email'] ?? '—') ?></td>
                                            <td style="font-size:13px; color:#888; white-space:nowrap;">
                                                <?= date('d/m/Y H:i', strtotime($log['created_at'])) ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

    </div>
</section>