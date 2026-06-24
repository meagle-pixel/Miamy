<?php
/** @var int   $nb_restaurants_total  */
/** @var int   $nb_restaurants_actifs */
/** @var int   $nb_utilisateurs       */
/** @var int   $nb_plats              */
/** @var int   $nb_commandes_jour     */
/** @var float $ca_jour               */
/** @var int   $nb_promos_actives     */
/** @var array $derniers_utilisateurs */
/** @var array $derniers_restaurants  */
/** @var array $derniers_logs         */

// Valeurs par defaut au cas ou la vue serait appelee sans controller
$nb_restaurants_total  = $nb_restaurants_total  ?? 0;
$nb_restaurants_actifs = $nb_restaurants_actifs ?? 0;
$nb_utilisateurs       = $nb_utilisateurs       ?? 0;
$nb_plats              = $nb_plats              ?? 0;
$nb_commandes_jour     = $nb_commandes_jour     ?? 0;
$ca_jour               = $ca_jour               ?? 0;
$nb_promos_actives     = $nb_promos_actives     ?? 0;
$derniers_utilisateurs = $derniers_utilisateurs ?? [];
$derniers_restaurants  = $derniers_restaurants  ?? [];
$derniers_logs         = $derniers_logs         ?? [];


  // Retourne le style CSS d'un badge selon le type d'action de log.
 // Fonction de presentation uniquement : reste dans la vue.

if (!function_exists('getActionBadgeStyle')) {
    function getActionBadgeStyle($action_type) {
        switch ($action_type) {
            // Connexions
            case 'login':         return ['bg' => '#FFF8E1', 'text' => '#FF8F00']; // jaune
            case 'logout':        return ['bg' => '#FEEBEB', 'text' => '#B71C1C']; // rouge
            case 'login_fail':    return ['bg' => '#FFE0B2', 'text' => '#E65100']; // orange
            case 'connect_as':    return ['bg' => '#EDE7F6', 'text' => '#311B92']; // violet

            // Créations (en vert)
            case 'create_user':
            case 'create_page':   return ['bg' => '#E1F5EE', 'text' => '#085041']; // vert

            // Modifications (en bleu)
            case 'update_role':
            case 'update_page':
            case 'update_permission':
            case 'update_profile':
            case 'update_password': return ['bg' => '#E3F2FD', 'text' => '#0D47A1']; // bleu

            // Réinitialisation mot de passe
            case 'reset_password': return ['bg' => '#FFE0B2', 'text' => '#E65100']; // orange

            // Suppression (rouge foncé)
            case 'delete_user':   return ['bg' => '#FFCDD2', 'text' => '#7F0000']; // rouge foncé

            // Par défaut : gris neutre
            default:              return ['bg' => '#f0f0f0', 'text' => '#555'];
        }
    }
}
?>
<div class="container-fluid px-4">

    <!-- Titre + breadcrumb (style SB Admin) -->
    <h1 class="mt-4">Tableau de bord</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="<?= APP_URL ?>/accueil">Accueil</a></li>
        <li class="breadcrumb-item active">Admin</li>
    </ol>

    <!-- Titre de section -->
    <div class="row mb-4">
        <div class="col-12">
            <h3 class="mb-1">
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
                        <a href="<?= APP_URL ?>/admin-restaurants" class="btn btn-sm btn_theme" style="font-size:12px;">
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
                                                    <!-- Badge rouge = inactif -->
                                                    <span class="badge" style="background:#FEEBEB; color:#B71C1C; font-size:11px;">Inactif</span>
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
                        <a href="<?= APP_URL ?>/admin-panel" class="btn btn-sm btn_theme" style="font-size:12px;">
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
                                        <?php $badge = getActionBadgeStyle($log['action_type']); ?>
                                        <tr>
                                            <!-- Badge de type d'action (couleur selon action) -->
                                            <td>
                                                <span class="badge" style="background:<?= $badge['bg'] ?>; color:<?= $badge['text'] ?>; font-size:11px;">
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
