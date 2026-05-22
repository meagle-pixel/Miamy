<?php
// Détection de la page courante (pour le surlignage du menu)
$current_mod = $_GET['mod'] ?? '';
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <base href="<?= APP_URL ?>/">
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title><?= isset($page_title) ? htmlspecialchars($page_title) : 'Admin' ?> - Miamy</title>
    <link rel="icon" type="image/png" href="<?= APP_URL ?>/assets/img/favicon-96x96.png">
    <!-- Datatables -->
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
    <!-- SB Admin (contient déjà Bootstrap 5.2.3) -->
    <link href="<?= APP_URL ?>/assets/admins/css/styles.css" rel="stylesheet" />
    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/fontawesome.all.min.css" />
</head>

<body class="sb-nav-fixed">

    <!-- Topbar -->
    <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
        <a class="navbar-brand ps-3" href="<?= APP_URL ?>/accueil">Miamy Admin</a>
        <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!">
            <i class="fas fa-bars"></i>
        </button>

        <form class="d-none d-md-inline-block form-inline ms-auto me-0 me-md-3 my-2 my-md-0">
            <div class="input-group">
                <input class="form-control" type="text" placeholder="Rechercher..." aria-label="Rechercher" aria-describedby="btnNavbarSearch" />
                <button class="btn btn-primary" id="btnNavbarSearch" type="button">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </form>

        <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-user fa-fw"></i>
                </a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                    <li><a class="dropdown-item" href="<?= APP_URL ?>/mon-compte">Mon compte</a></li>
                    <li>
                        <hr class="dropdown-divider" />
                    </li>
                    <li><a class="dropdown-item" href="<?= APP_URL ?>/deconnexion">Déconnexion</a></li>
                </ul>
            </li>
        </ul>
    </nav>

    <div id="layoutSidenav">

        <!-- Sidebar -->
        <div id="layoutSidenav_nav">
            <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                <div class="sb-sidenav-menu">
                    <div class="nav">

                        <div class="sb-sidenav-menu-heading">Vue d'ensemble</div>
                        <a class="nav-link <?= $current_mod === 'dashboard' ? 'active' : '' ?>" href="<?= APP_URL ?>/dashboard">
                            <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                            Tableau de bord
                        </a>

                        <div class="sb-sidenav-menu-heading">Gestion</div>
                        <a class="nav-link <?= $current_mod === 'admin-restaurants' ? 'active' : '' ?>" href="<?= APP_URL ?>/admin-restaurants">
                            <div class="sb-nav-link-icon"><i class="fas fa-users"></i></div>
                            Restaurants
                        </a>
                        <a class="nav-link <?= $current_mod === 'admin-panel' ? 'active' : '' ?>" href="<?= APP_URL ?>/admin-panel">
                            <div class="sb-nav-link-icon"><i class="fas fa-users"></i></div>
                            Utilisateurs
                        </a>
                        <a class="nav-link <?= $current_mod === 'ajouter-admin' ? 'active' : '' ?>" href="<?= APP_URL ?>/ajouter-admin">
                            <div class="sb-nav-link-icon"><i class="fas fa-users"></i></div>
                            Ajouter un administrateur
                        </a>

                    </div>
                </div>
                <div class="sb-sidenav-footer">
                    <div class="small">Connecté en tant que :</div>
                    <?= htmlspecialchars($_SESSION['user']['nom'] ?? $_SESSION['user']['email'] ?? 'Admin') ?>
                </div>
            </nav>
        </div>

        <!-- Contenu -->
        <div id="layoutSidenav_content">
            <main>