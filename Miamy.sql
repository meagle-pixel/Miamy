

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET NAMES utf8mb4;
SET time_zone = "+00:00";



SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `autorisations`;
DROP TABLE IF EXISTS `configuration`;
DROP TABLE IF EXISTS `langues`;
DROP TABLE IF EXISTS `messages`;
DROP TABLE IF EXISTS `moyens_paiement`;
DROP TABLE IF EXISTS `profils`;
DROP TABLE IF EXISTS `promos`;
DROP TABLE IF EXISTS `status`;

DROP TABLE IF EXISTS `user_logs`;
DROP TABLE IF EXISTS `horaires`;
DROP TABLE IF EXISTS `plats`;
DROP TABLE IF EXISTS `restaurant_categories`;
DROP TABLE IF EXISTS `restaurants`;
DROP TABLE IF EXISTS `clients`;
DROP TABLE IF EXISTS `restaurateurs`;
DROP TABLE IF EXISTS `administrateurs`;
DROP TABLE IF EXISTS `utilisateurs`;
DROP TABLE IF EXISTS `civilites`;
DROP TABLE IF EXISTS `categories`;
DROP TABLE IF EXISTS `pages`;

SET FOREIGN_KEY_CHECKS = 1;

START TRANSACTION;



CREATE TABLE `civilites` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `libelle` varchar(250) NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE `categories` (
    `id_categorie` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(100) NOT NULL,
    `icon` varchar(100) DEFAULT NULL,
    `ordre` int(11) NOT NULL DEFAULT 0,
    PRIMARY KEY (`id_categorie`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE `pages` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `nom` varchar(255) NOT NULL,
    `mod` varchar(100) NOT NULL,
    `url` varchar(100) NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;


CREATE TABLE `utilisateurs` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `email` varchar(250) NOT NULL,
    `motdepasse` varchar(100) NOT NULL,
    `profil` int(11) NOT NULL DEFAULT 3,
    `profil_id` int(11) DEFAULT NULL,
    `dateinscription` datetime DEFAULT NULL,
    `dateconnect` datetime DEFAULT NULL,
    `dateaction` datetime DEFAULT NULL,
    `token` varchar(50) DEFAULT NULL,
    `actif` tinyint(1) NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`),
    UNIQUE KEY `email` (`email`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;



CREATE TABLE `administrateurs` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `nom` varchar(150) NOT NULL,
    `prenom` varchar(150) NOT NULL,
    `telephone` varchar(20) NOT NULL,
    `user_id` int(11) NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_admin_user` (`user_id`),
    CONSTRAINT `fk_admin_user` FOREIGN KEY (`user_id`)
        REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;


CREATE TABLE `restaurateurs` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `nom` varchar(100) DEFAULT NULL,
    `prenom` varchar(100) DEFAULT NULL,
    `email` varchar(150) DEFAULT NULL,
    `telephone` varchar(20) DEFAULT NULL,
    `user_id` int(11) NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `email` (`email`),
    UNIQUE KEY `uq_resto_user` (`user_id`),
    CONSTRAINT `fk_restaurateur_user` FOREIGN KEY (`user_id`)
        REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;


CREATE TABLE `clients` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `civilite` int(11) NOT NULL,
    `nom` varchar(150) NOT NULL,
    `prenom` varchar(150) NOT NULL,
    `telephone` varchar(20) NOT NULL,
    `adresse` text DEFAULT NULL,
    `adresse_comp` text NOT NULL,
    `codepostal` varchar(20) NOT NULL,
    `ville` varchar(200) NOT NULL,
    `user_id` int(11) NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_client_user` (`user_id`),
    CONSTRAINT `fk_client_user` FOREIGN KEY (`user_id`)
        REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_client_civilite` FOREIGN KEY (`civilite`)
        REFERENCES `civilites` (`id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;



CREATE TABLE `restaurants` (
    `id_restaurant` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(150) NOT NULL,
    `slug` varchar(150) NOT NULL,
    `description` text DEFAULT NULL,
    `city` varchar(100) DEFAULT NULL,
    `main_image` varchar(255) DEFAULT 'default-resto.jpg',
    `rating` decimal(2, 1) DEFAULT 0.0,
    `review_count` int(11) DEFAULT 0,
    `is_featured` tinyint(1) DEFAULT 0,
    `subscription_active` tinyint(1) DEFAULT 0,
    `created_at` timestamp NULL DEFAULT current_timestamp(),
    `id_restaurateur` int(11) NOT NULL,
    PRIMARY KEY (`id_restaurant`),
    UNIQUE KEY `slug` (`slug`),
    CONSTRAINT `fk_resto_patron` FOREIGN KEY (`id_restaurateur`)
        REFERENCES `restaurateurs` (`id`) ON DELETE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE `restaurant_categories` (
    `id_categorie` int(11) NOT NULL,
    `id_restaurant` int(11) NOT NULL,
    PRIMARY KEY (`id_categorie`, `id_restaurant`),
    CONSTRAINT `fk_cle_categorie` FOREIGN KEY (`id_categorie`)
        REFERENCES `categories` (`id_categorie`) ON DELETE CASCADE,
    CONSTRAINT `fk_cle_restaurant` FOREIGN KEY (`id_restaurant`)
        REFERENCES `restaurants` (`id_restaurant`) ON DELETE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE `plats` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `nom` varchar(150) NOT NULL,
    `description` text DEFAULT NULL,
    `prix` decimal(8, 2) NOT NULL DEFAULT 0.00,
    `image` varchar(255) DEFAULT NULL,
    `categorie` varchar(100) NOT NULL DEFAULT 'Plats',
    `id_restaurant` int(11) NOT NULL,
    `disponible` tinyint(1) NOT NULL DEFAULT 1,
    `created_at` timestamp NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`),
    KEY `id_restaurant` (`id_restaurant`),
    CONSTRAINT `fk_plat_restaurant` FOREIGN KEY (`id_restaurant`)
        REFERENCES `restaurants` (`id_restaurant`) ON DELETE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE `horaires` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `id_restaurant` int(11) NOT NULL,
    `jour` tinyint(1) NOT NULL COMMENT '0=Lundi, 6=Dimanche',
    `ouvert` tinyint(1) NOT NULL DEFAULT 1,
    `debut` time DEFAULT NULL,
    `fin` time DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_resto_jour` (`id_restaurant`, `jour`),
    KEY `idx_resto_jour` (`id_restaurant`, `jour`),
    CONSTRAINT `fk_horaires_restaurant` FOREIGN KEY (`id_restaurant`)
        REFERENCES `restaurants` (`id_restaurant`) ON DELETE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;



CREATE TABLE `user_logs` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `user_id` int(11) NOT NULL,
    `action_type` varchar(50) NOT NULL,
    `message` varchar(255) NOT NULL,
    `ip_address` varchar(45) DEFAULT NULL,
    `created_at` datetime DEFAULT current_timestamp(),
    PRIMARY KEY (`id`),
    KEY `idx_user_log` (`user_id`),
    KEY `idx_date_log` (`created_at`),
    CONSTRAINT `fk_userlog_user` FOREIGN KEY (`user_id`)
        REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;



INSERT INTO `civilites` (`id`, `libelle`) VALUES
    (1, 'M.'),
    (2, 'Mme'),
    (3, 'Mlle');

INSERT INTO `categories` (`name`, `icon`, `ordre`) VALUES
    ('Français',      'fa-wine-glass',      1),
    ('Italien',       'fa-pizza-slice',     2),
    ('Japonais',      'fa-fish',            3),
    ('Sushi',         'fa-fish',            4),
    ('Chinois',       'fa-bowl-rice',       5),
    ('Indien',        'fa-pepper-hot',      6),
    ('Mexicain',      'fa-pepper-hot',      7),
    ('Libanais',      'fa-leaf',            8),
    ('Thaïlandais',   'fa-shrimp',          9),
    ('Burger',        'fa-burger',         10),
    ('Fast-food',     'fa-burger',         11),
    ('Pizza',         'fa-pizza-slice',    12),
    ('Kebab',         'fa-drumstick-bite', 13),
    ('Brasserie',     'fa-beer-mug-empty', 14),
    ('Américain',     'fa-burger',         15),
    ('Végétarien',    'fa-carrot',         16),
    ('Fruits de mer', 'fa-shrimp',         17),
    ('Gastronomique', 'fa-star',           18);

INSERT INTO `pages` (`nom`, `mod`, `url`) VALUES
    ('Accueil',                  'accueil',                 'views/home.php'),
    ('À propos',                 'a-propos',                'views/a-propos.php'),
    ('FAQ',                      'faq',                     'views/faq.php'),
    ('Contact',                  'contact',                 'views/contact.php'),
    ('Liste des restaurants',    'liste-restaurants',       'views/liste-restaurants.php'),
    ('Connexion',                'connexion',               'views/login.php'),
    ('Inscription',              'inscription',             'views/register.php'),
    ('Inscription-client',       'inscription-client',      'views/register-client.php'),
    ('Déconnexion',              'deconnexion',             'views/deconnexion.php'),
    ('Mon compte',               'mon-compte',              'views/mon-compte.php'),
    ('Mon compte restaurateur',  'mon-compte-restaurateur', 'views/mon-compte-restaurateur.php'),
    ('Profil',                   'profil',                  'views/profile.php'),
    ('Modifier profil',          'profil-editer',           'views/profil-editer.php'),
    ('Ajouter un restaurant',    'ajouter-restaurant',      'views/ajouter-restaurant.php'),
    ('Modifier un restaurant',   'modifier-restaurant',     'views/modifier-restaurant.php'),
    ('Supprimer un restaurant',  'supprimer-restaurant',    'views/supprimer-restaurant.php'),
    ('Gestion de la carte',      'gestion-carte',           'views/gestion-carte.php'),
    ('Ajouter un plat',          'ajouter-plat',            'views/ajouter-plat.php'),
    ('Modifier un plat',         'modifier-plat',           'views/modifier-plat.php'),
    ('Supprimer un plat',        'supprimer-plat',          'views/supprimer-plat.php'),
    ('Commande',                 'commande',                'views/commande.php'),
    ('Mes commandes',            'commandes',               'views/commandes.php'),
    ('Détails restaurant',       'details',                 'views/details.php'),
    ('Admin Panel',              'admin-panel',             'views/admin/admin-panel.php'),
    ('Dashboard',                'dashboard',               'views/admin/dashboard.php'),
    ('Ajouter un administrateur','ajouter-admin',           'views/admin/ajouter-admin.php'),
    ('Vue admin restaurants',    'admin-restaurants',       'views/admin/admin-restaurants.php'),
    ('Liste des plats',          'liste-plats',             'views/liste-plats.php');

COMMIT;
