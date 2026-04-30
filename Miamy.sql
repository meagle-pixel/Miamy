

-- ============================================================
-- Miamy - Base de données (export prod)
-- ============================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET NAMES utf8mb4;
SET time_zone = "+00:00";

START TRANSACTION;

CREATE TABLE `administrateurs` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `nom` varchar(150) NOT NULL,
    `prenom` varchar(150) NOT NULL,
    `telephone` varchar(20) NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE `autorisations` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `page` int(11) NOT NULL,
    `profil` int(11) NOT NULL,
    `etat` int(11) NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE `categories` (
    `id_categorie` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(100) NOT NULL,
    `icon` varchar(100) DEFAULT NULL,
    `ordre` int(11) NOT NULL DEFAULT 0,
    PRIMARY KEY (`id_categorie`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE `civilites` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `libelle` varchar(250) NOT NULL,
    `lang` int(11) NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE `clients` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `civilite` tinyint(4) NOT NULL,
    `nom` varchar(150) NOT NULL,
    `prenom` varchar(150) NOT NULL,
    `telephone` varchar(20) NOT NULL,
    `adresse` text DEFAULT NULL,
    `adresse_comp` text NOT NULL,
    `codepostal` varchar(20) NOT NULL,
    `ville` varchar(200) NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE `configuration` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(50) NOT NULL,
    `proper_name` varchar(50) NOT NULL,
    `value` varchar(300) NOT NULL,
    `order` int(11) NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE `langues` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `nom` varchar(50) NOT NULL,
    `code` varchar(10) NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE `messages` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `expediteur` int(11) NOT NULL,
    `destinataire` int(11) NOT NULL,
    `message` text NOT NULL,
    `date` timestamp NOT NULL DEFAULT current_timestamp(),
    `unread` tinyint(1) NOT NULL DEFAULT 1,
    `delete` tinyint(4) NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE `moyens_paiement` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `nom` varchar(30) NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE `pages` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `nom` varchar(255) NOT NULL,
    `mod` varchar(100) NOT NULL,
    `url` varchar(100) NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE `profils` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `libelle` varchar(50) NOT NULL,
    `type` varchar(50) NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE `promos` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `code` text NOT NULL,
    `percent` int(11) NOT NULL,
    `actif` int(11) NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE `restaurateurs` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `nom` varchar(100) DEFAULT NULL,
    `prenom` varchar(100) DEFAULT NULL,
    `email` varchar(150) DEFAULT NULL,
    `telephone` varchar(20) DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `email` (`email`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE `status` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `libelle` varchar(100) NOT NULL,
    PRIMARY KEY (`id`)
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
    KEY `idx_date_log` (`created_at`)
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
    CONSTRAINT `fk_resto_patron` FOREIGN KEY (`id_restaurateur`) REFERENCES `restaurateurs` (`id`) ON DELETE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE `restaurant_categories` (
    `id_categorie` int(11) NOT NULL,
    `id_restaurant` int(11) NOT NULL,
    PRIMARY KEY (`id_categorie`, `id_restaurant`),
    CONSTRAINT `fk_cle_categorie` FOREIGN KEY (`id_categorie`) REFERENCES `categories` (`id_categorie`) ON DELETE CASCADE,
    CONSTRAINT `fk_cle_restaurant` FOREIGN KEY (`id_restaurant`) REFERENCES `restaurants` (`id_restaurant`) ON DELETE CASCADE
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
    CONSTRAINT `plats_ibfk_1` FOREIGN KEY (`id_restaurant`) REFERENCES `restaurants` (`id_restaurant`) ON DELETE CASCADE
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
    CONSTRAINT `fk_horaires_restaurant` FOREIGN KEY (`id_restaurant`) REFERENCES `restaurants` (`id_restaurant`) ON DELETE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;


INSERT INTO `civilites` (`id`, `libelle`, `lang`) VALUES
    (1, 'M.',   1),
    (2, 'Mme',  1),
    (3, 'Mlle', 1);

INSERT INTO `langues` (`id`, `nom`, `code`) VALUES
    (1, 'Français', 'fr'),
    (2, 'Anglais',  'en');

INSERT INTO `profils` (`id`, `libelle`, `type`) VALUES
    (1, 'Administrateurs',        'administrateurs'),
    (2, 'Restaurants',            'restaurateurs'),
    (3, 'Clients de restaurants', 'clients');

INSERT INTO `status` (`id`, `libelle`) VALUES
    (1, 'Paiement en attente'),
    (2, 'Paiement accepté'),
    (3, 'Commande en cours de préparation'),
    (4, 'Commande finalisée'),
    (5, 'Commande annulée');

INSERT INTO `moyens_paiement` (`id`, `nom`) VALUES
    (1, 'Carte Bancaire');

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
    ('Ajouter un administrateur','ajouter-admin',           'views/admin/ajouter-admin.php');

-- Donne tous les droits à l'admin (profil 1) sur toutes les pages
INSERT INTO `autorisations` (`page`, `profil`, `etat`)
SELECT `id`, 1, 1 FROM `pages`;

INSERT INTO `pages` (`nom`, `mod`, `url`) VALUES
('Vue admin restaurants', 'admin-restaurants', 'views/admin/admin-restaurants.php');

COMMIT;