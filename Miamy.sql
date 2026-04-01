-- ============================================================
-- Miamy - Base de données propre
-- Généré le : 2026-04-01
-- ============================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";
SET NAMES utf8mb4;

-- ============================================================
-- SUPPRESSION ET RECRÉATION DES TABLES
-- ============================================================


-- ============================================================
-- STRUCTURE DES TABLES
-- ============================================================


CREATE TABLE `administrateurs` (
    `id`        int(11)      NOT NULL AUTO_INCREMENT,
    `nom`       varchar(150) NOT NULL,
    `prenom`    varchar(150) NOT NULL,
    `telephone` varchar(20)  NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

CREATE TABLE `autorisations` (
    `id`     int(11) NOT NULL AUTO_INCREMENT,
    `page`   int(11) NOT NULL,
    `profil` int(11) NOT NULL,
    `etat`   int(11) NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

CREATE TABLE `categories` (
    `id`    int(11)      NOT NULL AUTO_INCREMENT,
    `name`  varchar(100) NOT NULL,
    `icon`  varchar(100) DEFAULT NULL,
    `ordre` int(11)      NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

CREATE TABLE `civilites` (
    `id`      int(11)      NOT NULL AUTO_INCREMENT,
    `libelle` varchar(250) NOT NULL,
    `lang`    int(11)      NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

CREATE TABLE `clients` (
    `id`           int(11)      NOT NULL AUTO_INCREMENT,
    `civilite`     tinyint(4)   NOT NULL,
    `nom`          varchar(150) NOT NULL,
    `prenom`       varchar(150) NOT NULL,
    `telephone`    varchar(20)  NOT NULL,
    `adresse`      text         DEFAULT NULL,
    `adresse_comp` text         NOT NULL,
    `codepostal`   varchar(20)  NOT NULL,
    `ville`        varchar(200) NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

CREATE TABLE `configuration` (
    `id`          int(11)      NOT NULL AUTO_INCREMENT,
    `name`        varchar(50)  NOT NULL,
    `proper_name` varchar(50)  NOT NULL,
    `value`       varchar(300) NOT NULL,
    `order`       int(11)      NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_bin;

-- --------------------------------------------------------

CREATE TABLE `ips` (
    `ip`        varchar(50) NOT NULL,
    `user`      int(11)     NOT NULL,
    `user_type` int(11)     NOT NULL,
    `infos`     text        NOT NULL,
    `date`      timestamp   NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

CREATE TABLE `langues` (
    `id`   int(11)     NOT NULL AUTO_INCREMENT,
    `nom`  varchar(50) NOT NULL,
    `code` varchar(10) NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

CREATE TABLE `messages` (
    `id`           int(11)    NOT NULL AUTO_INCREMENT,
    `expediteur`   int(11)    NOT NULL,
    `destinataire` int(11)    NOT NULL,
    `message`      text       NOT NULL,
    `date`         timestamp  NOT NULL DEFAULT current_timestamp(),
    `unread`       tinyint(1) NOT NULL DEFAULT 1,
    `delete`       tinyint(4) NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

CREATE TABLE `moyens_paiement` (
    `id`  int(11)     NOT NULL AUTO_INCREMENT,
    `nom` varchar(30) NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

CREATE TABLE `pages` (
    `id`  int(11)      NOT NULL AUTO_INCREMENT,
    `nom` varchar(255) NOT NULL,
    `mod` varchar(100) NOT NULL,
    `url` varchar(100) NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

CREATE TABLE `profils` (
    `id`      int(11)     NOT NULL AUTO_INCREMENT,
    `libelle` varchar(50) NOT NULL,
    `type`    varchar(50) NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

CREATE TABLE `promos` (
    `id`      int(11) NOT NULL AUTO_INCREMENT,
    `code`    text    NOT NULL,
    `percent` int(11) NOT NULL,
    `actif`   int(11) NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

CREATE TABLE `restaurants` (
    `id`                  int(11)      NOT NULL AUTO_INCREMENT,
    `name`                varchar(150) NOT NULL,
    `slug`                varchar(150) NOT NULL,
    `description`         text         DEFAULT NULL,
    `city`                varchar(100) DEFAULT NULL,
    `main_image`          varchar(255) DEFAULT 'default-resto.jpg',
    `category_id`         int(11)      DEFAULT NULL,
    `rating`              decimal(2,1) DEFAULT 0.0,
    `review_count`        int(11)      DEFAULT 0,
    `is_featured`         tinyint(1)   DEFAULT 0,
    `subscription_active` tinyint(1)   DEFAULT 0,
    `created_at`          timestamp    NULL DEFAULT current_timestamp(),
    `id_restaurateur`     int(11)      NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `slug` (`slug`),
    KEY `category_id` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

CREATE TABLE `restaurateurs` (
    `id`              int(11)      NOT NULL AUTO_INCREMENT,
    `nom`             varchar(100) DEFAULT NULL,
    `prenom`          varchar(100) DEFAULT NULL,
    `email`           varchar(150) DEFAULT NULL,
    `telephone`       varchar(20)  DEFAULT NULL,
    `dateinscription` timestamp    NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`),
    UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

CREATE TABLE `status` (
    `id`      int(11)      NOT NULL AUTO_INCREMENT,
    `libelle` varchar(100) NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

CREATE TABLE `user_logs` (
    `id`          int(11)      NOT NULL AUTO_INCREMENT,
    `user_id`     int(11)      NOT NULL,
    `action_type` varchar(50)  NOT NULL,
    `message`     varchar(255) NOT NULL,
    `ip_address`  varchar(45)  DEFAULT NULL,
    `created_at`  datetime     DEFAULT current_timestamp(),
    PRIMARY KEY (`id`),
    KEY `idx_user_log` (`user_id`),
    KEY `idx_date_log` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

CREATE TABLE `utilisateurs` (
    `id`              int(11)      NOT NULL AUTO_INCREMENT,
    `email`           varchar(250) NOT NULL,
    `motdepasse`      varchar(100) NOT NULL,
    `profil`          int(11)      NOT NULL DEFAULT 3,
    `profil_id`       int(11)      DEFAULT NULL,
    `dateinscription` datetime     DEFAULT NULL,
    `dateconnect`     datetime     DEFAULT NULL,
    `dateaction`      datetime     DEFAULT NULL,
    `token`           varchar(50)  DEFAULT NULL,
    `actif`           tinyint(1)   NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`),
    UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

CREATE TABLE `plats` (
    `id`            int(11)        NOT NULL AUTO_INCREMENT,
    `nom`           varchar(150)   NOT NULL,
    `description`   text           DEFAULT NULL,
    `prix`          decimal(8,2)   NOT NULL DEFAULT 0.00,
    `image`         varchar(255)   DEFAULT NULL,
    `categorie`     varchar(100)   NOT NULL DEFAULT 'Plats',
    `id_restaurant` int(11)        NOT NULL,
    `disponible`    tinyint(1)     NOT NULL DEFAULT 1,
    `created_at`    timestamp      NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`),
    KEY `id_restaurant` (`id_restaurant`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================
-- CONTRAINTES
-- ============================================================

ALTER TABLE `restaurants`
    ADD CONSTRAINT `restaurants_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`);

ALTER TABLE `plats`
    ADD CONSTRAINT `plats_ibfk_1` FOREIGN KEY (`id_restaurant`) REFERENCES `restaurants` (`id`) ON DELETE CASCADE;

-- ============================================================
-- DONNÉES DE RÉFÉRENCE
-- ============================================================

-- Civilités
INSERT INTO `civilites` (`id`, `libelle`, `lang`) VALUES
(1, 'M.',   1),
(2, 'Mme',  1),
(3, 'Mlle', 1);

-- Langues
INSERT INTO `langues` (`id`, `nom`, `code`) VALUES
(1, 'Français', 'fr'),
(2, 'Anglais',  'en');

-- Profils
INSERT INTO `profils` (`id`, `libelle`, `type`) VALUES
(1, 'Administrateurs',        'administrateurs'),
(2, 'Restaurants',            'restaurateurs'),
(3, 'Clients de restaurants', 'clients');

-- Statuts de commande
INSERT INTO `status` (`id`, `libelle`) VALUES
(1, 'Paiement en attente'),
(2, 'Paiement accepté'),
(3, 'Commande en cours de préparation'),
(4, 'Commande finalisée'),
(5, 'Commande annulée');

-- Moyens de paiement
INSERT INTO `moyens_paiement` (`id`, `nom`) VALUES
(1, 'Carte Bancaire');

-- Catégories de restaurants (types de cuisine)
INSERT INTO `categories` (`name`, `icon`, `ordre`) VALUES
('Français',      'fa-wine-glass',     1),
('Italien',       'fa-pizza-slice',    2),
('Japonais',      'fa-fish',           3),
('Sushi',         'fa-fish',           4),
('Chinois',       'fa-bowl-rice',      5),
('Indien',        'fa-pepper-hot',     6),
('Mexicain',      'fa-pepper-hot',     7),
('Libanais',      'fa-leaf',           8),
('Thaïlandais',   'fa-shrimp',         9),
('Burger',        'fa-burger',         10),
('Fast-food',     'fa-burger',         11),
('Pizza',         'fa-pizza-slice',    12),
('Kebab',         'fa-drumstick-bite', 13),
('Brasserie',     'fa-beer-mug-empty', 14),
('Américain',     'fa-burger',         15),
('Végétarien',    'fa-carrot',         16),
('Fruits de mer', 'fa-shrimp',         17),
('Gastronomique', 'fa-star',           18);

-- Pages du site
INSERT INTO `pages` (`nom`, `mod`, `url`) VALUES
('Accueil',               'accueil',                'views/home.php'),
('À propos',              'a-propos',               'views/a-propos.php'),
('FAQ',                   'faq',                    'views/faq.php'),
('Contact',               'contact',                'views/contact.php'),
('Liste des restaurants', 'liste-restaurants',      'views/liste-restaurants.php'),
('Connexion',             'connexion',              'views/login.php'),
('Inscription',           'inscription',            'views/register.php'),
('Déconnexion',           'deconnexion',            'views/deconnexion.php'),
('Mon compte',            'mon-compte',             'views/mon-compte.php'),
('Mon compte restaurateur','mon-compte-restaurateur','views/mon-compte-restaurateur.php'),
('Profil',                'profil',                 'views/profile.php'),
('Modifier profil',       'profil-editer',          'views/profil-editer.php'),
('Ajouter un restaurant', 'ajouter-restaurant',     'views/ajouter-restaurant.php'),
('Modifier un restaurant','modifier-restaurant',    'views/modifier-restaurant.php'),
('Supprimer un restaurant','supprimer-restaurant',  'views/supprimer-restaurant.php'),
('Gestion de la carte',   'gestion-carte',          'views/gestion-carte.php'),
('Ajouter un plat',       'ajouter-plat',           'views/ajouter-plat.php'),
('Modifier un plat',      'modifier-plat',          'views/modifier-plat.php'),
('Supprimer un plat',     'supprimer-plat',         'views/supprimer-plat.php'),
('Commande',              'commande',               'views/commande.php'),
('Mes commandes',         'commandes',              'views/commandes.php');

-- Autorisations (toutes les pages accessibles au profil admin = 1)
INSERT INTO `autorisations` (`page`, `profil`, `etat`)
SELECT `id`, 1, 1 FROM `pages`;

COMMIT;
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    