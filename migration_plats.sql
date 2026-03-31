-- ============================================================
-- Migration : Gestion des plats (Miamy)
-- À importer dans phpMyAdmin ou via MySQL
-- ============================================================

-- 1. Créer la table des plats
-- ============================================================

CREATE TABLE IF NOT EXISTS `plats` (
    `id`            int(11)         NOT NULL AUTO_INCREMENT,
    `nom`           varchar(200)    NOT NULL,
    `description`   text            DEFAULT NULL,
    `prix`          decimal(10,2)   NOT NULL DEFAULT 0.00,
    `image`         varchar(255)    DEFAULT NULL,
    `categorie`     varchar(100)    NOT NULL DEFAULT 'Plats',
    `id_restaurant` int(11)         NOT NULL,
    `disponible`    tinyint(1)      NOT NULL DEFAULT 1,
    `created_at`    datetime        DEFAULT current_timestamp(),
    PRIMARY KEY (`id`),
    KEY `idx_restaurant` (`id_restaurant`),
    KEY `idx_disponible` (`disponible`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- 2. Ajouter les nouvelles pages dans le routeur
-- ============================================================




-- 3. Créer le dossier pour les images des plats
-- ============================================================
-- ⚠️  À faire manuellement sur le serveur :
--     mkdir -p /var/www/html/Miamy/assets/img/plats
--     chmod 755 /var/www/html/Miamy/assets/img/plats
--
-- Ou via phpMyAdmin → Aller dans le dossier assets/img/
-- et créer un sous-dossier "plats"
-- ============================================================
