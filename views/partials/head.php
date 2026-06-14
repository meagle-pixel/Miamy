<!DOCTYPE html>
<html lang="fr">

<head>
    <base href="<?= APP_URL ?>/">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Préconnexion + polices Google Fonts (chargées tôt, sans bloquer le rendu) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&family=Roboto:ital,wght@0,300;0,400;0,500;0,700&display=swap">
    <!-- Title -->
    <title><?php if(isset($page_title)){ echo $page_title; } else { ?> Miamy - Le menu interactif de votre restaurant<?php } ?></title>
    <!-- Bootstrap css -->
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/assets/css/bootstrap.min.css" />
    <!-- animate css -->
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/assets/css/animate.min.css" />
    <!-- Fontawesome css -->
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/assets/css/fontawesome.all.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.2/font/bootstrap-icons.css">
    <!-- owl.carousel css -->
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/assets/css/owl.carousel.min.css" />
	<!-- Rangeslider css -->
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/assets/css/nouislider.css" />
    <!-- owl.theme.default css -->
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/assets/css/owl.theme.default.min.css" />
    <!-- navber css -->
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/assets/css/navber.css" />
    <!-- meanmenu css -->
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/assets/css/meanmenu.css" />
    <!-- Style css -->
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/assets/css/style.css" />
    <!-- Responsive css -->
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/assets/css/responsive.css" />
    <!-- Responsive fixes (overrides du template Foodingly) -->
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/assets/css/responsive-fixes.css" />
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?php echo APP_URL; ?>/assets/img/favicon-96x96.png">
    
</head>
<body>