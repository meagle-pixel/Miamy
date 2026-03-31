-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost:3306
-- Généré le : ven. 27 mars 2026 à 08:55
-- Version du serveur : 11.4.10-MariaDB
-- Version de PHP : 8.4.18

USE Miamy;

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";

START TRANSACTION;

SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */
;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */
;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */
;
/*!40101 SET NAMES utf8mb4 */
;

--
-- Base de données : `sc1feti6921_miamy`
--

-- --------------------------------------------------------

--
-- Structure de la table `administrateurs`
--

CREATE TABLE `administrateurs` (
    `id` int(11) NOT NULL,
    `nom` varchar(150) NOT NULL,
    `prenom` varchar(150) NOT NULL,
    `telephone` varchar(20) NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = latin1 COLLATE = latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Structure de la table `autorisations`
--

CREATE TABLE `autorisations` (
    `id` int(11) NOT NULL,
    `page` int(11) NOT NULL,
    `profil` int(11) NOT NULL,
    `etat` int(11) NOT NULL DEFAULT 0
) ENGINE = InnoDB DEFAULT CHARSET = latin1 COLLATE = latin1_swedish_ci;

--
-- Déchargement des données de la table `autorisations`
--

INSERT INTO
    `autorisations` (
        `id`,
        `page`,
        `profil`,
        `etat`
    )
VALUES (1, 1, 1, 1),
    (2, 2, 1, 1),
    (3, 3, 1, 1),
    (4, 4, 1, 1),
    (5, 5, 1, 1);

-- --------------------------------------------------------

--
-- Structure de la table `categories`
--

CREATE TABLE `categories` (
    `id` int(11) NOT NULL,
    `name` varchar(100) NOT NULL,
    `icon` varchar(100) DEFAULT NULL
) ENGINE = InnoDB DEFAULT CHARSET = latin1 COLLATE = latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Structure de la table `civilites`
--

CREATE TABLE `civilites` (
    `id` int(11) NOT NULL,
    `libelle` varchar(250) NOT NULL,
    `lang` int(11) NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb3 COLLATE = utf8mb3_general_ci;

--
-- Déchargement des données de la table `civilites`
--

INSERT INTO
    `civilites` (`id`, `libelle`, `lang`)
VALUES (1, 'M.', 1),
    (2, 'Mme', 1),
    (3, 'Mlle', 1);

-- --------------------------------------------------------

--
-- Structure de la table `clients`
--

CREATE TABLE `clients` (
    `id` int(11) NOT NULL,
    `civilite` tinyint(4) NOT NULL,
    `nom` varchar(150) NOT NULL,
    `prenom` varchar(150) NOT NULL,
    `telephone` varchar(20) NOT NULL,
    `adresse` text DEFAULT NULL,
    `adresse_comp` text NOT NULL,
    `codepostal` varchar(20) NOT NULL,
    `ville` varchar(200) NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = latin1 COLLATE = latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Structure de la table `configuration`
--

CREATE TABLE `configuration` (
    `id` int(11) NOT NULL,
    `name` varchar(50) NOT NULL,
    `proper_name` varchar(50) NOT NULL,
    `value` varchar(300) NOT NULL,
    `order` int(11) NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb3 COLLATE = utf8mb3_bin;

-- --------------------------------------------------------

--
-- Structure de la table `ips`
--

CREATE TABLE `ips` (
    `ip` varchar(50) NOT NULL,
    `user` int(11) NOT NULL,
    `user_type` int(11) NOT NULL,
    `infos` text NOT NULL,
    `date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE = InnoDB DEFAULT CHARSET = latin1 COLLATE = latin1_swedish_ci;

--
-- Déchargement des données de la table `ips`
--

INSERT INTO
    `ips` (
        `ip`,
        `user`,
        `user_type`,
        `infos`,
        `date`
    )
VALUES (
        '88.122.145.134',
        1,
        1,
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/106.0.0.0 Safari/537.36',
        '2022-10-31 06:52:29'
    );

-- --------------------------------------------------------

--
-- Structure de la table `langues`
--

CREATE TABLE `langues` (
    `id` int(11) NOT NULL,
    `nom` varchar(50) NOT NULL,
    `code` varchar(10) NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = latin1 COLLATE = latin1_swedish_ci;

--
-- Déchargement des données de la table `langues`
--

INSERT INTO
    `langues` (`id`, `nom`, `code`)
VALUES (1, 'Français', 'fr'),
    (2, 'Anglais', 'en');

-- --------------------------------------------------------

--
-- Structure de la table `messages`
--

CREATE TABLE `messages` (
    `id` int(11) NOT NULL,
    `expediteur` int(11) NOT NULL,
    `destinataire` int(11) NOT NULL,
    `message` text NOT NULL,
    `date` timestamp NOT NULL DEFAULT current_timestamp(),
    `unread` tinyint(1) NOT NULL DEFAULT 1,
    `delete` tinyint(4) NOT NULL DEFAULT 0
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

--
-- Déchargement des données de la table `messages`
--

INSERT INTO
    `messages` (
        `id`,
        `expediteur`,
        `destinataire`,
        `message`,
        `date`,
        `unread`,
        `delete`
    )
VALUES (
        1,
        1,
        1,
        'C\'est moi ça va ?',
        '2022-10-13 10:36:14',
        0,
        0
    ),
    (
        2,
        1,
        1,
        'nkjkjjkjkjk',
        '2022-10-13 11:47:49',
        0,
        0
    ),
    (
        3,
        2,
        1,
        'hgjhfgjkfdgjhd hjhj hhdgj hdjhfg h',
        '2022-10-13 12:26:15',
        0,
        0
    ),
    (
        4,
        1,
        2,
        'jkhkjhgkjhg jkhjkhjkhjk hjkhj hj hjkh jkhjhjk hj hjhj hjh hjh jh jmj mjml jmljjghi gjhgfhg\r\n\r\nljksdhsdljhljhjsdhsdhsdhdsdskjhdskjh\r\n\r\n\r\nsdkjhsdkjhjskhkjhkjsdhks\r\n\r\njkhkjhgkjhg jkhjkhjkhjk hjkhj hj hjkh jkhjhjk hj hjhj hjh hjh jh jmj mjml jmljjghi gjhgfhg\r\n\r\nljksdhsdljhljhjsdhsdhsdhdsdskjhdskjh\r\n\r\n\r\nsdkjhsdkjhjskhkjhkjsdhks\r\n\r\njkhkjhgkjhg jkhjkhjkhjk hjkhj hj hjkh jkhjhjk hj hjhj hjh hjh jh jmj mjml jmljjghi gjhgfhg\r\n\r\nljksdhsdljhljhjsdhsdhsdhdsdskjhdskjh\r\n\r\n\r\nsdkjhsdkjhjskhkjhkjsdhks\r\n\r\njkhkjhgkjhg jkhjkhjkhjk hjkhj hj hjkh jkhjhjk hj hjhj hjh hjh jh jmj mjml jmljjghi gjhgfhg\r\n\r\nljksdhsdljhljhjsdhsdhsdhdsdskjhdskjh\r\n\r\n\r\nsdkjhsdkjhjskhkjhkjsdhks\r\n\r\njkhkjhgkjhg jkhjkhjkhjk hjkhj hj hjkh jkhjhjk hj hjhj hjh hjh jh jmj mjml jmljjghi gjhgfhg\r\n\r\nljksdhsdljhljhjsdhsdhsdhdsdskjhdskjh\r\n\r\n\r\nsdkjhsdkjhjskhkjhkjsdhks\r\n\r\njkhkjhgkjhg jkhjkhjkhjk hjkhj hj hjkh jkhjhjk hj hjhj hjh hjh jh jmj mjml jmljjghi gjhgfhg\r\n\r\nljksdhsdljhljhjsdhsdhsdhdsdskjhdskjh\r\n\r\n\r\nsdkjhsdkjhjskhkjhkjsdhks\r\n\r\njkhkjhgkjhg jkhjkhjkhjk hjkhj hj hjkh jkhjhjk hj hjhj hjh hjh jh jmj mjml jmljjghi gjhgfhg\r\n\r\nljksdhsdljhljhjsdhsdhsdhdsdskjhdskjh\r\n\r\n\r\nsdkjhsdkjhjskhkjhkjsdhks\r\n\r\njkhkjhgkjhg jkhjkhjkhjk hjkhj hj hjkh jkhjhjk hj hjhj hjh hjh jh jmj mjml jmljjghi gjhgfhg\r\n\r\nljksdhsdljhljhjsdhsdhsdhdsdskjhdskjh\r\n\r\n\r\nsdkjhsdkjhjskhkjhkjsdhks\r\n\r\njkhkjhgkjhg jkhjkhjkhjk hjkhj hj hjkh jkhjhjk hj hjhj hjh hjh jh jmj mjml jmljjghi gjhgfhg\r\n\r\nljksdhsdljhljhjsdhsdhsdhdsdskjhdskjh\r\n\r\n\r\nsdkjhsdkjhjskhkjhkjsdhks\r\n\r\njkhkjhgkjhg jkhjkhjkhjk hjkhj hj hjkh jkhjhjk hj hjhj hjh hjh jh jmj mjml jmljjghi gjhgfhg\r\n\r\nljksdhsdljhljhjsdhsdhsdhdsdskjhdskjh\r\n\r\n\r\nsdkjhsdkjhjskhkjhkjsdhks',
        '2022-10-13 12:26:31',
        0,
        0
    ),
    (
        5,
        2,
        1,
        'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam dictum turpis at fringilla consectetur. Sed eget sem diam. Phasellus sit amet lobortis sem. Integer vitae porta massa. Praesent commodo lectus ac eleifend porttitor. Nam non fringilla nibh. Nam condimentum scelerisque ipsum, ac hendrerit neque egestas sed.\r\n\r\nOrci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Etiam faucibus libero at metus consequat iaculis. Quisque interdum porttitor erat vel lobortis. Phasellus venenatis urna elit, at congue libero varius quis. Nam vestibulum urna gravida, condimentum dui sed, scelerisque risus. Duis sed ante vel odio vulputate rutrum ac et arcu. Sed tincidunt orci diam, tempor faucibus ligula vulputate vulputate. Pellentesque varius eget felis sit amet sodales. Maecenas nunc purus, congue vitae scelerisque at, ultricies at quam. Mauris pulvinar lacus a erat maximus fringilla. Sed iaculis magna a sollicitudin dapibus. Duis ac tempor lorem, vel semper nunc. Donec nisl mauris, sagittis ac purus non, volutpat volutpat erat. Nunc nisl sem, lacinia id ante in, efficitur dignissim est. Maecenas malesuada, est at auctor condimentum, nisi turpis efficitur tortor, vel dictum turpis nisi in tellus.\r\n\r\nIn hendrerit massa vitae egestas faucibus. Quisque sed elementum lectus. Aenean fermentum felis velit, et imperdiet urna molestie sed. Praesent sed pharetra tellus, vitae euismod arcu. Proin imperdiet eros ac ipsum laoreet eleifend. Proin molestie ultrices quam nec commodo. Ut dictum lobortis nisl, sit amet consequat sem auctor id. Integer tincidunt hendrerit erat, at rutrum lectus porttitor id.\r\n\r\nCras lectus tellus, commodo et ultrices eu, egestas ac nunc. Vivamus auctor in magna in rutrum. Mauris at orci eu nisi egestas vulputate. Aenean non purus ante. Phasellus sollicitudin tristique diam, quis tempus lorem placerat et. Vivamus at dolor ipsum. Integer eget hendrerit risus, eget consectetur nisi. Nunc leo quam, tincidunt non justo id, interdum tempus arcu. Nulla vitae accumsan erat. Nunc euismod leo sed sapien convallis, eu volutpat nulla aliquam. Quisque facilisis sollicitudin felis eget mollis. Cras laoreet placerat commodo. Etiam egestas diam vel nulla luctus, vel euismod diam dictum. Morbi eleifend nibh a est tincidunt ultricies. Maecenas vel malesuada metus.\r\n\r\nCurabitur porttitor diam at nulla luctus, id luctus nunc pellentesque. Aliquam erat volutpat. Suspendisse posuere sem libero, ut dignissim ex convallis sagittis. Suspendisse placerat tortor nibh, non pellentesque nibh viverra nec. Cras vestibulum ac nulla sed dictum. Nulla facilisi. Integer at sapien porta, mattis justo in, luctus augue. Integer vitae tristique elit. Nullam congue mauris quis arcu iaculis, sit amet fermentum magna faucibus. Phasellus elementum nisi quis dui venenatis ornare. Nulla eleifend lectus a commodo maximus. Interdum et malesuada fames ac ante ipsum primis in faucibus. Donec gravida, enim a lacinia rhoncus, metus dolor pellentesque leo, eu facilisis orci metus et eros. Nunc elementum sollicitudin suscipit. Fusce in lorem dui. Aliquam sit amet augue faucibus, hendrerit tellus vestibulum, dapibus lacus.',
        '2022-10-13 13:07:05',
        0,
        0
    ),
    (
        6,
        2,
        1,
        'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam dictum turpis at fringilla consectetur. Sed eget sem diam. Phasellus sit amet lobortis sem. Integer vitae porta massa. Praesent commodo lectus ac eleifend porttitor. Nam non fringilla nibh. Nam condimentum scelerisque ipsum, ac hendrerit neque egestas sed.\r\n\r\nOrci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Etiam faucibus libero at metus consequat iaculis. Quisque interdum porttitor erat vel lobortis. Phasellus venenatis urna elit, at congue libero varius quis. Nam vestibulum urna gravida, condimentum dui sed, scelerisque risus. Duis sed ante vel odio vulputate rutrum ac et arcu. Sed tincidunt orci diam, tempor faucibus ligula vulputate vulputate. Pellentesque varius eget felis sit amet sodales. Maecenas nunc purus, congue vitae scelerisque at, ultricies at quam. Mauris pulvinar lacus a erat maximus fringilla. Sed iaculis magna a sollicitudin dapibus. Duis ac tempor lorem, vel semper nunc. Donec nisl mauris, sagittis ac purus non, volutpat volutpat erat. Nunc nisl sem, lacinia id ante in, efficitur dignissim est. Maecenas malesuada, est at auctor condimentum, nisi turpis efficitur tortor, vel dictum turpis nisi in tellus.\r\n\r\nIn hendrerit massa vitae egestas faucibus. Quisque sed elementum lectus. Aenean fermentum felis velit, et imperdiet urna molestie sed. Praesent sed pharetra tellus, vitae euismod arcu. Proin imperdiet eros ac ipsum laoreet eleifend. Proin molestie ultrices quam nec commodo. Ut dictum lobortis nisl, sit amet consequat sem auctor id. Integer tincidunt hendrerit erat, at rutrum lectus porttitor id.\r\n\r\nCras lectus tellus, commodo et ultrices eu, egestas ac nunc. Vivamus auctor in magna in rutrum. Mauris at orci eu nisi egestas vulputate. Aenean non purus ante. Phasellus sollicitudin tristique diam, quis tempus lorem placerat et. Vivamus at dolor ipsum. Integer eget hendrerit risus, eget consectetur nisi. Nunc leo quam, tincidunt non justo id, interdum tempus arcu. Nulla vitae accumsan erat. Nunc euismod leo sed sapien convallis, eu volutpat nulla aliquam. Quisque facilisis sollicitudin felis eget mollis. Cras laoreet placerat commodo. Etiam egestas diam vel nulla luctus, vel euismod diam dictum. Morbi eleifend nibh a est tincidunt ultricies. Maecenas vel malesuada metus.\r\n\r\nCurabitur porttitor diam at nulla luctus, id luctus nunc pellentesque. Aliquam erat volutpat. Suspendisse posuere sem libero, ut dignissim ex convallis sagittis. Suspendisse placerat tortor nibh, non pellentesque nibh viverra nec. Cras vestibulum ac nulla sed dictum. Nulla facilisi. Integer at sapien porta, mattis justo in, luctus augue. Integer vitae tristique elit. Nullam congue mauris quis arcu iaculis, sit amet fermentum magna faucibus. Phasellus elementum nisi quis dui venenatis ornare. Nulla eleifend lectus a commodo maximus. Interdum et malesuada fames ac ante ipsum primis in faucibus. Donec gravida, enim a lacinia rhoncus, metus dolor pellentesque leo, eu facilisis orci metus et eros. Nunc elementum sollicitudin suscipit. Fusce in lorem dui. Aliquam sit amet augue faucibus, hendrerit tellus vestibulum, dapibus lacus.',
        '2022-10-13 13:07:05',
        0,
        0
    ),
    (
        7,
        1,
        2,
        'jkhkjhgkjhg jkhjkhjkhjk hjkhj hj hjkh jkhjhjk hj hjhj hjh hjh jh jmj mjml jmljjghi gjhgfhg\r\n\r\nljksdhsdljhljhjsdhsdhsdhdsdskjhdskjh\r\n\r\n\r\nsdkjhsdkjhjskhkjhkjsdhks\r\n\r\njkhkjhgkjhg jkhjkhjkhjk hjkhj hj hjkh jkhjhjk hj hjhj hjh hjh jh jmj mjml jmljjghi gjhgfhg\r\n\r\nljksdhsdljhljhjsdhsdhsdhdsdskjhdskjh\r\n\r\n\r\nsdkjhsdkjhjskhkjhkjsdhks\r\n\r\njkhkjhgkjhg jkhjkhjkhjk hjkhj hj hjkh jkhjhjk hj hjhj hjh hjh jh jmj mjml jmljjghi gjhgfhg\r\n\r\nljksdhsdljhljhjsdhsdhsdhdsdskjhdskjh\r\n\r\n\r\nsdkjhsdkjhjskhkjhkjsdhks\r\n\r\njkhkjhgkjhg jkhjkhjkhjk hjkhj hj hjkh jkhjhjk hj hjhj hjh hjh jh jmj mjml jmljjghi gjhgfhg\r\n\r\nljksdhsdljhljhjsdhsdhsdhdsdskjhdskjh\r\n\r\n\r\nsdkjhsdkjhjskhkjhkjsdhks\r\n\r\njkhkjhgkjhg jkhjkhjkhjk hjkhj hj hjkh jkhjhjk hj hjhj hjh hjh jh jmj mjml jmljjghi gjhgfhg\r\n\r\nljksdhsdljhljhjsdhsdhsdhdsdskjhdskjh\r\n\r\n\r\nsdkjhsdkjhjskhkjhkjsdhks\r\n\r\njkhkjhgkjhg jkhjkhjkhjk hjkhj hj hjkh jkhjhjk hj hjhj hjh hjh jh jmj mjml jmljjghi gjhgfhg\r\n\r\nljksdhsdljhljhjsdhsdhsdhdsdskjhdskjh\r\n\r\n\r\nsdkjhsdkjhjskhkjhkjsdhks\r\n\r\njkhkjhgkjhg jkhjkhjkhjk hjkhj hj hjkh jkhjhjk hj hjhj hjh hjh jh jmj mjml jmljjghi gjhgfhg\r\n\r\nljksdhsdljhljhjsdhsdhsdhdsdskjhdskjh\r\n\r\n\r\nsdkjhsdkjhjskhkjhkjsdhks\r\n\r\njkhkjhgkjhg jkhjkhjkhjk hjkhj hj hjkh jkhjhjk hj hjhj hjh hjh jh jmj mjml jmljjghi gjhgfhg\r\n\r\nljksdhsdljhljhjsdhsdhsdhdsdskjhdskjh\r\n\r\n\r\nsdkjhsdkjhjskhkjhkjsdhks\r\n\r\njkhkjhgkjhg jkhjkhjkhjk hjkhj hj hjkh jkhjhjk hj hjhj hjh hjh jh jmj mjml jmljjghi gjhgfhg\r\n\r\nljksdhsdljhljhjsdhsdhsdhdsdskjhdskjh\r\n\r\n\r\nsdkjhsdkjhjskhkjhkjsdhks\r\n\r\njkhkjhgkjhg jkhjkhjkhjk hjkhj hj hjkh jkhjhjk hj hjhj hjh hjh jh jmj mjml jmljjghi gjhgfhg\r\n\r\nljksdhsdljhljhjsdhsdhsdhdsdskjhdskjh\r\n\r\n\r\nsdkjhsdkjhjskhkjhkjsdhks',
        '2022-10-13 12:26:31',
        0,
        0
    ),
    (
        8,
        2,
        1,
        'hgjhfgjkfdgjhd hjhj hhdgj hdjhfg h',
        '2022-10-13 12:26:15',
        0,
        0
    ),
    (
        9,
        1,
        1,
        'nkjkjjkjkjk',
        '2022-10-13 11:47:49',
        0,
        0
    ),
    (
        10,
        1,
        1,
        'C\'est moi ça va ?',
        '2022-10-13 10:36:14',
        0,
        0
    ),
    (
        11,
        2,
        1,
        'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam dictum turpis at fringilla consectetur. Sed eget sem diam. Phasellus sit amet lobortis sem. Integer vitae porta massa. Praesent commodo lectus ac eleifend porttitor. Nam non fringilla nibh. Nam condimentum scelerisque ipsum, ac hendrerit neque egestas sed.\r\n\r\nOrci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Etiam faucibus libero at metus consequat iaculis. Quisque interdum porttitor erat vel lobortis. Phasellus venenatis urna elit, at congue libero varius quis. Nam vestibulum urna gravida, condimentum dui sed, scelerisque risus. Duis sed ante vel odio vulputate rutrum ac et arcu. Sed tincidunt orci diam, tempor faucibus ligula vulputate vulputate. Pellentesque varius eget felis sit amet sodales. Maecenas nunc purus, congue vitae scelerisque at, ultricies at quam. Mauris pulvinar lacus a erat maximus fringilla. Sed iaculis magna a sollicitudin dapibus. Duis ac tempor lorem, vel semper nunc. Donec nisl mauris, sagittis ac purus non, volutpat volutpat erat. Nunc nisl sem, lacinia id ante in, efficitur dignissim est. Maecenas malesuada, est at auctor condimentum, nisi turpis efficitur tortor, vel dictum turpis nisi in tellus.\r\n\r\nIn hendrerit massa vitae egestas faucibus. Quisque sed elementum lectus. Aenean fermentum felis velit, et imperdiet urna molestie sed. Praesent sed pharetra tellus, vitae euismod arcu. Proin imperdiet eros ac ipsum laoreet eleifend. Proin molestie ultrices quam nec commodo. Ut dictum lobortis nisl, sit amet consequat sem auctor id. Integer tincidunt hendrerit erat, at rutrum lectus porttitor id.\r\n\r\nCras lectus tellus, commodo et ultrices eu, egestas ac nunc. Vivamus auctor in magna in rutrum. Mauris at orci eu nisi egestas vulputate. Aenean non purus ante. Phasellus sollicitudin tristique diam, quis tempus lorem placerat et. Vivamus at dolor ipsum. Integer eget hendrerit risus, eget consectetur nisi. Nunc leo quam, tincidunt non justo id, interdum tempus arcu. Nulla vitae accumsan erat. Nunc euismod leo sed sapien convallis, eu volutpat nulla aliquam. Quisque facilisis sollicitudin felis eget mollis. Cras laoreet placerat commodo. Etiam egestas diam vel nulla luctus, vel euismod diam dictum. Morbi eleifend nibh a est tincidunt ultricies. Maecenas vel malesuada metus.\r\n\r\nCurabitur porttitor diam at nulla luctus, id luctus nunc pellentesque. Aliquam erat volutpat. Suspendisse posuere sem libero, ut dignissim ex convallis sagittis. Suspendisse placerat tortor nibh, non pellentesque nibh viverra nec. Cras vestibulum ac nulla sed dictum. Nulla facilisi. Integer at sapien porta, mattis justo in, luctus augue. Integer vitae tristique elit. Nullam congue mauris quis arcu iaculis, sit amet fermentum magna faucibus. Phasellus elementum nisi quis dui venenatis ornare. Nulla eleifend lectus a commodo maximus. Interdum et malesuada fames ac ante ipsum primis in faucibus. Donec gravida, enim a lacinia rhoncus, metus dolor pellentesque leo, eu facilisis orci metus et eros. Nunc elementum sollicitudin suscipit. Fusce in lorem dui. Aliquam sit amet augue faucibus, hendrerit tellus vestibulum, dapibus lacus.',
        '2022-10-13 13:07:05',
        0,
        0
    ),
    (
        12,
        1,
        2,
        'jkhkjhgkjhg jkhjkhjkhjk hjkhj hj hjkh jkhjhjk hj hjhj hjh hjh jh jmj mjml jmljjghi gjhgfhg\r\n\r\nljksdhsdljhljhjsdhsdhsdhdsdskjhdskjh\r\n\r\n\r\nsdkjhsdkjhjskhkjhkjsdhks\r\n\r\njkhkjhgkjhg jkhjkhjkhjk hjkhj hj hjkh jkhjhjk hj hjhj hjh hjh jh jmj mjml jmljjghi gjhgfhg\r\n\r\nljksdhsdljhljhjsdhsdhsdhdsdskjhdskjh\r\n\r\n\r\nsdkjhsdkjhjskhkjhkjsdhks\r\n\r\njkhkjhgkjhg jkhjkhjkhjk hjkhj hj hjkh jkhjhjk hj hjhj hjh hjh jh jmj mjml jmljjghi gjhgfhg\r\n\r\nljksdhsdljhljhjsdhsdhsdhdsdskjhdskjh\r\n\r\n\r\nsdkjhsdkjhjskhkjhkjsdhks\r\n\r\njkhkjhgkjhg jkhjkhjkhjk hjkhj hj hjkh jkhjhjk hj hjhj hjh hjh jh jmj mjml jmljjghi gjhgfhg\r\n\r\nljksdhsdljhljhjsdhsdhsdhdsdskjhdskjh\r\n\r\n\r\nsdkjhsdkjhjskhkjhkjsdhks\r\n\r\njkhkjhgkjhg jkhjkhjkhjk hjkhj hj hjkh jkhjhjk hj hjhj hjh hjh jh jmj mjml jmljjghi gjhgfhg\r\n\r\nljksdhsdljhljhjsdhsdhsdhdsdskjhdskjh\r\n\r\n\r\nsdkjhsdkjhjskhkjhkjsdhks\r\n\r\njkhkjhgkjhg jkhjkhjkhjk hjkhj hj hjkh jkhjhjk hj hjhj hjh hjh jh jmj mjml jmljjghi gjhgfhg\r\n\r\nljksdhsdljhljhjsdhsdhsdhdsdskjhdskjh\r\n\r\n\r\nsdkjhsdkjhjskhkjhkjsdhks\r\n\r\njkhkjhgkjhg jkhjkhjkhjk hjkhj hj hjkh jkhjhjk hj hjhj hjh hjh jh jmj mjml jmljjghi gjhgfhg\r\n\r\nljksdhsdljhljhjsdhsdhsdhdsdskjhdskjh\r\n\r\n\r\nsdkjhsdkjhjskhkjhkjsdhks\r\n\r\njkhkjhgkjhg jkhjkhjkhjk hjkhj hj hjkh jkhjhjk hj hjhj hjh hjh jh jmj mjml jmljjghi gjhgfhg\r\n\r\nljksdhsdljhljhjsdhsdhsdhdsdskjhdskjh\r\n\r\n\r\nsdkjhsdkjhjskhkjhkjsdhks\r\n\r\njkhkjhgkjhg jkhjkhjkhjk hjkhj hj hjkh jkhjhjk hj hjhj hjh hjh jh jmj mjml jmljjghi gjhgfhg\r\n\r\nljksdhsdljhljhjsdhsdhsdhdsdskjhdskjh\r\n\r\n\r\nsdkjhsdkjhjskhkjhkjsdhks\r\n\r\njkhkjhgkjhg jkhjkhjkhjk hjkhj hj hjkh jkhjhjk hj hjhj hjh hjh jh jmj mjml jmljjghi gjhgfhg\r\n\r\nljksdhsdljhljhjsdhsdhsdhdsdskjhdskjh\r\n\r\n\r\nsdkjhsdkjhjskhkjhkjsdhks',
        '2022-10-13 12:26:31',
        0,
        0
    ),
    (
        13,
        2,
        1,
        'hgjhfgjkfdgjhd hjhj hhdgj hdjhfg h',
        '2022-10-13 12:26:15',
        0,
        0
    ),
    (
        14,
        1,
        1,
        'nkjkjjkjkjk',
        '2022-10-13 11:47:49',
        0,
        0
    ),
    (
        15,
        1,
        1,
        'C\'est moi ça va ?',
        '2022-10-13 10:36:14',
        0,
        0
    ),
    (
        16,
        2,
        1,
        'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam dictum turpis at fringilla consectetur. Sed eget sem diam. Phasellus sit amet lobortis sem. Integer vitae porta massa. Praesent commodo lectus ac eleifend porttitor. Nam non fringilla nibh. Nam condimentum scelerisque ipsum, ac hendrerit neque egestas sed.\r\n\r\nOrci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Etiam faucibus libero at metus consequat iaculis. Quisque interdum porttitor erat vel lobortis. Phasellus venenatis urna elit, at congue libero varius quis. Nam vestibulum urna gravida, condimentum dui sed, scelerisque risus. Duis sed ante vel odio vulputate rutrum ac et arcu. Sed tincidunt orci diam, tempor faucibus ligula vulputate vulputate. Pellentesque varius eget felis sit amet sodales. Maecenas nunc purus, congue vitae scelerisque at, ultricies at quam. Mauris pulvinar lacus a erat maximus fringilla. Sed iaculis magna a sollicitudin dapibus. Duis ac tempor lorem, vel semper nunc. Donec nisl mauris, sagittis ac purus non, volutpat volutpat erat. Nunc nisl sem, lacinia id ante in, efficitur dignissim est. Maecenas malesuada, est at auctor condimentum, nisi turpis efficitur tortor, vel dictum turpis nisi in tellus.\r\n\r\nIn hendrerit massa vitae egestas faucibus. Quisque sed elementum lectus. Aenean fermentum felis velit, et imperdiet urna molestie sed. Praesent sed pharetra tellus, vitae euismod arcu. Proin imperdiet eros ac ipsum laoreet eleifend. Proin molestie ultrices quam nec commodo. Ut dictum lobortis nisl, sit amet consequat sem auctor id. Integer tincidunt hendrerit erat, at rutrum lectus porttitor id.\r\n\r\nCras lectus tellus, commodo et ultrices eu, egestas ac nunc. Vivamus auctor in magna in rutrum. Mauris at orci eu nisi egestas vulputate. Aenean non purus ante. Phasellus sollicitudin tristique diam, quis tempus lorem placerat et. Vivamus at dolor ipsum. Integer eget hendrerit risus, eget consectetur nisi. Nunc leo quam, tincidunt non justo id, interdum tempus arcu. Nulla vitae accumsan erat. Nunc euismod leo sed sapien convallis, eu volutpat nulla aliquam. Quisque facilisis sollicitudin felis eget mollis. Cras laoreet placerat commodo. Etiam egestas diam vel nulla luctus, vel euismod diam dictum. Morbi eleifend nibh a est tincidunt ultricies. Maecenas vel malesuada metus.\r\n\r\nCurabitur porttitor diam at nulla luctus, id luctus nunc pellentesque. Aliquam erat volutpat. Suspendisse posuere sem libero, ut dignissim ex convallis sagittis. Suspendisse placerat tortor nibh, non pellentesque nibh viverra nec. Cras vestibulum ac nulla sed dictum. Nulla facilisi. Integer at sapien porta, mattis justo in, luctus augue. Integer vitae tristique elit. Nullam congue mauris quis arcu iaculis, sit amet fermentum magna faucibus. Phasellus elementum nisi quis dui venenatis ornare. Nulla eleifend lectus a commodo maximus. Interdum et malesuada fames ac ante ipsum primis in faucibus. Donec gravida, enim a lacinia rhoncus, metus dolor pellentesque leo, eu facilisis orci metus et eros. Nunc elementum sollicitudin suscipit. Fusce in lorem dui. Aliquam sit amet augue faucibus, hendrerit tellus vestibulum, dapibus lacus.',
        '2022-10-13 13:07:05',
        0,
        0
    ),
    (
        17,
        1,
        2,
        'jkhkjhgkjhg jkhjkhjkhjk hjkhj hj hjkh jkhjhjk hj hjhj hjh hjh jh jmj mjml jmljjghi gjhgfhg\r\n\r\nljksdhsdljhljhjsdhsdhsdhdsdskjhdskjh\r\n\r\n\r\nsdkjhsdkjhjskhkjhkjsdhks\r\n\r\njkhkjhgkjhg jkhjkhjkhjk hjkhj hj hjkh jkhjhjk hj hjhj hjh hjh jh jmj mjml jmljjghi gjhgfhg\r\n\r\nljksdhsdljhljhjsdhsdhsdhdsdskjhdskjh\r\n\r\n\r\nsdkjhsdkjhjskhkjhkjsdhks\r\n\r\njkhkjhgkjhg jkhjkhjkhjk hjkhj hj hjkh jkhjhjk hj hjhj hjh hjh jh jmj mjml jmljjghi gjhgfhg\r\n\r\nljksdhsdljhljhjsdhsdhsdhdsdskjhdskjh\r\n\r\n\r\nsdkjhsdkjhjskhkjhkjsdhks\r\n\r\njkhkjhgkjhg jkhjkhjkhjk hjkhj hj hjkh jkhjhjk hj hjhj hjh hjh jh jmj mjml jmljjghi gjhgfhg\r\n\r\nljksdhsdljhljhjsdhsdhsdhdsdskjhdskjh\r\n\r\n\r\nsdkjhsdkjhjskhkjhkjsdhks\r\n\r\njkhkjhgkjhg jkhjkhjkhjk hjkhj hj hjkh jkhjhjk hj hjhj hjh hjh jh jmj mjml jmljjghi gjhgfhg\r\n\r\nljksdhsdljhljhjsdhsdhsdhdsdskjhdskjh\r\n\r\n\r\nsdkjhsdkjhjskhkjhkjsdhks\r\n\r\njkhkjhgkjhg jkhjkhjkhjk hjkhj hj hjkh jkhjhjk hj hjhj hjh hjh jh jmj mjml jmljjghi gjhgfhg\r\n\r\nljksdhsdljhljhjsdhsdhsdhdsdskjhdskjh\r\n\r\n\r\nsdkjhsdkjhjskhkjhkjsdhks\r\n\r\njkhkjhgkjhg jkhjkhjkhjk hjkhj hj hjkh jkhjhjk hj hjhj hjh hjh jh jmj mjml jmljjghi gjhgfhg\r\n\r\nljksdhsdljhljhjsdhsdhsdhdsdskjhdskjh\r\n\r\n\r\nsdkjhsdkjhjskhkjhkjsdhks\r\n\r\njkhkjhgkjhg jkhjkhjkhjk hjkhj hj hjkh jkhjhjk hj hjhj hjh hjh jh jmj mjml jmljjghi gjhgfhg\r\n\r\nljksdhsdljhljhjsdhsdhsdhdsdskjhdskjh\r\n\r\n\r\nsdkjhsdkjhjskhkjhkjsdhks\r\n\r\njkhkjhgkjhg jkhjkhjkhjk hjkhj hj hjkh jkhjhjk hj hjhj hjh hjh jh jmj mjml jmljjghi gjhgfhg\r\n\r\nljksdhsdljhljhjsdhsdhsdhdsdskjhdskjh\r\n\r\n\r\nsdkjhsdkjhjskhkjhkjsdhks\r\n\r\njkhkjhgkjhg jkhjkhjkhjk hjkhj hj hjkh jkhjhjk hj hjhj hjh hjh jh jmj mjml jmljjghi gjhgfhg\r\n\r\nljksdhsdljhljhjsdhsdhsdhdsdskjhdskjh\r\n\r\n\r\nsdkjhsdkjhjskhkjhkjsdhks',
        '2022-10-13 12:26:31',
        0,
        0
    ),
    (
        18,
        2,
        1,
        'hgjhfgjkfdgjhd hjhj hhdgj hdjhfg h',
        '2022-10-13 12:26:15',
        0,
        0
    ),
    (
        19,
        1,
        1,
        'nkjkjjkjkjk',
        '2022-10-13 11:47:49',
        0,
        0
    ),
    (
        20,
        1,
        1,
        'C\'est moi ça va ?',
        '2022-10-13 10:36:14',
        0,
        0
    ),
    (
        21,
        2,
        1,
        'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam dictum turpis at fringilla consectetur. Sed eget sem diam. Phasellus sit amet lobortis sem. Integer vitae porta massa. Praesent commodo lectus ac eleifend porttitor. Nam non fringilla nibh. Nam condimentum scelerisque ipsum, ac hendrerit neque egestas sed.\r\n\r\nOrci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Etiam faucibus libero at metus consequat iaculis. Quisque interdum porttitor erat vel lobortis. Phasellus venenatis urna elit, at congue libero varius quis. Nam vestibulum urna gravida, condimentum dui sed, scelerisque risus. Duis sed ante vel odio vulputate rutrum ac et arcu. Sed tincidunt orci diam, tempor faucibus ligula vulputate vulputate. Pellentesque varius eget felis sit amet sodales. Maecenas nunc purus, congue vitae scelerisque at, ultricies at quam. Mauris pulvinar lacus a erat maximus fringilla. Sed iaculis magna a sollicitudin dapibus. Duis ac tempor lorem, vel semper nunc. Donec nisl mauris, sagittis ac purus non, volutpat volutpat erat. Nunc nisl sem, lacinia id ante in, efficitur dignissim est. Maecenas malesuada, est at auctor condimentum, nisi turpis efficitur tortor, vel dictum turpis nisi in tellus.\r\n\r\nIn hendrerit massa vitae egestas faucibus. Quisque sed elementum lectus. Aenean fermentum felis velit, et imperdiet urna molestie sed. Praesent sed pharetra tellus, vitae euismod arcu. Proin imperdiet eros ac ipsum laoreet eleifend. Proin molestie ultrices quam nec commodo. Ut dictum lobortis nisl, sit amet consequat sem auctor id. Integer tincidunt hendrerit erat, at rutrum lectus porttitor id.\r\n\r\nCras lectus tellus, commodo et ultrices eu, egestas ac nunc. Vivamus auctor in magna in rutrum. Mauris at orci eu nisi egestas vulputate. Aenean non purus ante. Phasellus sollicitudin tristique diam, quis tempus lorem placerat et. Vivamus at dolor ipsum. Integer eget hendrerit risus, eget consectetur nisi. Nunc leo quam, tincidunt non justo id, interdum tempus arcu. Nulla vitae accumsan erat. Nunc euismod leo sed sapien convallis, eu volutpat nulla aliquam. Quisque facilisis sollicitudin felis eget mollis. Cras laoreet placerat commodo. Etiam egestas diam vel nulla luctus, vel euismod diam dictum. Morbi eleifend nibh a est tincidunt ultricies. Maecenas vel malesuada metus.\r\n\r\nCurabitur porttitor diam at nulla luctus, id luctus nunc pellentesque. Aliquam erat volutpat. Suspendisse posuere sem libero, ut dignissim ex convallis sagittis. Suspendisse placerat tortor nibh, non pellentesque nibh viverra nec. Cras vestibulum ac nulla sed dictum. Nulla facilisi. Integer at sapien porta, mattis justo in, luctus augue. Integer vitae tristique elit. Nullam congue mauris quis arcu iaculis, sit amet fermentum magna faucibus. Phasellus elementum nisi quis dui venenatis ornare. Nulla eleifend lectus a commodo maximus. Interdum et malesuada fames ac ante ipsum primis in faucibus. Donec gravida, enim a lacinia rhoncus, metus dolor pellentesque leo, eu facilisis orci metus et eros. Nunc elementum sollicitudin suscipit. Fusce in lorem dui. Aliquam sit amet augue faucibus, hendrerit tellus vestibulum, dapibus lacus.',
        '2022-10-13 13:07:05',
        0,
        0
    ),
    (
        22,
        1,
        2,
        'jkhkjhgkjhg jkhjkhjkhjk hjkhj hj hjkh jkhjhjk hj hjhj hjh hjh jh jmj mjml jmljjghi gjhgfhg\r\n\r\nljksdhsdljhljhjsdhsdhsdhdsdskjhdskjh\r\n\r\n\r\nsdkjhsdkjhjskhkjhkjsdhks\r\n\r\njkhkjhgkjhg jkhjkhjkhjk hjkhj hj hjkh jkhjhjk hj hjhj hjh hjh jh jmj mjml jmljjghi gjhgfhg\r\n\r\nljksdhsdljhljhjsdhsdhsdhdsdskjhdskjh\r\n\r\n\r\nsdkjhsdkjhjskhkjhkjsdhks\r\n\r\njkhkjhgkjhg jkhjkhjkhjk hjkhj hj hjkh jkhjhjk hj hjhj hjh hjh jh jmj mjml jmljjghi gjhgfhg\r\n\r\nljksdhsdljhljhjsdhsdhsdhdsdskjhdskjh\r\n\r\n\r\nsdkjhsdkjhjskhkjhkjsdhks\r\n\r\njkhkjhgkjhg jkhjkhjkhjk hjkhj hj hjkh jkhjhjk hj hjhj hjh hjh jh jmj mjml jmljjghi gjhgfhg\r\n\r\nljksdhsdljhljhjsdhsdhsdhdsdskjhdskjh\r\n\r\n\r\nsdkjhsdkjhjskhkjhkjsdhks\r\n\r\njkhkjhgkjhg jkhjkhjkhjk hjkhj hj hjkh jkhjhjk hj hjhj hjh hjh jh jmj mjml jmljjghi gjhgfhg\r\n\r\nljksdhsdljhljhjsdhsdhsdhdsdskjhdskjh\r\n\r\n\r\nsdkjhsdkjhjskhkjhkjsdhks\r\n\r\njkhkjhgkjhg jkhjkhjkhjk hjkhj hj hjkh jkhjhjk hj hjhj hjh hjh jh jmj mjml jmljjghi gjhgfhg\r\n\r\nljksdhsdljhljhjsdhsdhsdhdsdskjhdskjh\r\n\r\n\r\nsdkjhsdkjhjskhkjhkjsdhks\r\n\r\njkhkjhgkjhg jkhjkhjkhjk hjkhj hj hjkh jkhjhjk hj hjhj hjh hjh jh jmj mjml jmljjghi gjhgfhg\r\n\r\nljksdhsdljhljhjsdhsdhsdhdsdskjhdskjh\r\n\r\n\r\nsdkjhsdkjhjskhkjhkjsdhks\r\n\r\njkhkjhgkjhg jkhjkhjkhjk hjkhj hj hjkh jkhjhjk hj hjhj hjh hjh jh jmj mjml jmljjghi gjhgfhg\r\n\r\nljksdhsdljhljhjsdhsdhsdhdsdskjhdskjh\r\n\r\n\r\nsdkjhsdkjhjskhkjhkjsdhks\r\n\r\njkhkjhgkjhg jkhjkhjkhjk hjkhj hj hjkh jkhjhjk hj hjhj hjh hjh jh jmj mjml jmljjghi gjhgfhg\r\n\r\nljksdhsdljhljhjsdhsdhsdhdsdskjhdskjh\r\n\r\n\r\nsdkjhsdkjhjskhkjhkjsdhks\r\n\r\njkhkjhgkjhg jkhjkhjkhjk hjkhj hj hjkh jkhjhjk hj hjhj hjh hjh jh jmj mjml jmljjghi gjhgfhg\r\n\r\nljksdhsdljhljhjsdhsdhsdhdsdskjhdskjh\r\n\r\n\r\nsdkjhsdkjhjskhkjhkjsdhks',
        '2022-10-13 12:26:31',
        0,
        0
    ),
    (
        23,
        2,
        1,
        'hgjhfgjkfdgjhd hjhj hhdgj hdjhfg h',
        '2022-10-13 12:26:15',
        0,
        0
    ),
    (
        24,
        1,
        1,
        'nkjkjjkjkjk',
        '2022-10-13 11:47:49',
        0,
        0
    ),
    (
        25,
        1,
        1,
        'C\'est moi ça va ?',
        '2022-10-13 10:36:14',
        0,
        0
    ),
    (
        26,
        2,
        1,
        'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam dictum turpis at fringilla consectetur. Sed eget sem diam. Phasellus sit amet lobortis sem. Integer vitae porta massa. Praesent commodo lectus ac eleifend porttitor. Nam non fringilla nibh. Nam condimentum scelerisque ipsum, ac hendrerit neque egestas sed.\r\n\r\nOrci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Etiam faucibus libero at metus consequat iaculis. Quisque interdum porttitor erat vel lobortis. Phasellus venenatis urna elit, at congue libero varius quis. Nam vestibulum urna gravida, condimentum dui sed, scelerisque risus. Duis sed ante vel odio vulputate rutrum ac et arcu. Sed tincidunt orci diam, tempor faucibus ligula vulputate vulputate. Pellentesque varius eget felis sit amet sodales. Maecenas nunc purus, congue vitae scelerisque at, ultricies at quam. Mauris pulvinar lacus a erat maximus fringilla. Sed iaculis magna a sollicitudin dapibus. Duis ac tempor lorem, vel semper nunc. Donec nisl mauris, sagittis ac purus non, volutpat volutpat erat. Nunc nisl sem, lacinia id ante in, efficitur dignissim est. Maecenas malesuada, est at auctor condimentum, nisi turpis efficitur tortor, vel dictum turpis nisi in tellus.\r\n\r\nIn hendrerit massa vitae egestas faucibus. Quisque sed elementum lectus. Aenean fermentum felis velit, et imperdiet urna molestie sed. Praesent sed pharetra tellus, vitae euismod arcu. Proin imperdiet eros ac ipsum laoreet eleifend. Proin molestie ultrices quam nec commodo. Ut dictum lobortis nisl, sit amet consequat sem auctor id. Integer tincidunt hendrerit erat, at rutrum lectus porttitor id.\r\n\r\nCras lectus tellus, commodo et ultrices eu, egestas ac nunc. Vivamus auctor in magna in rutrum. Mauris at orci eu nisi egestas vulputate. Aenean non purus ante. Phasellus sollicitudin tristique diam, quis tempus lorem placerat et. Vivamus at dolor ipsum. Integer eget hendrerit risus, eget consectetur nisi. Nunc leo quam, tincidunt non justo id, interdum tempus arcu. Nulla vitae accumsan erat. Nunc euismod leo sed sapien convallis, eu volutpat nulla aliquam. Quisque facilisis sollicitudin felis eget mollis. Cras laoreet placerat commodo. Etiam egestas diam vel nulla luctus, vel euismod diam dictum. Morbi eleifend nibh a est tincidunt ultricies. Maecenas vel malesuada metus.\r\n\r\nCurabitur porttitor diam at nulla luctus, id luctus nunc pellentesque. Aliquam erat volutpat. Suspendisse posuere sem libero, ut dignissim ex convallis sagittis. Suspendisse placerat tortor nibh, non pellentesque nibh viverra nec. Cras vestibulum ac nulla sed dictum. Nulla facilisi. Integer at sapien porta, mattis justo in, luctus augue. Integer vitae tristique elit. Nullam congue mauris quis arcu iaculis, sit amet fermentum magna faucibus. Phasellus elementum nisi quis dui venenatis ornare. Nulla eleifend lectus a commodo maximus. Interdum et malesuada fames ac ante ipsum primis in faucibus. Donec gravida, enim a lacinia rhoncus, metus dolor pellentesque leo, eu facilisis orci metus et eros. Nunc elementum sollicitudin suscipit. Fusce in lorem dui. Aliquam sit amet augue faucibus, hendrerit tellus vestibulum, dapibus lacus.',
        '2022-10-13 13:07:05',
        0,
        0
    ),
    (
        27,
        1,
        2,
        'jkhkjhgkjhg jkhjkhjkhjk hjkhj hj hjkh jkhjhjk hj hjhj hjh hjh jh jmj mjml jmljjghi gjhgfhg\r\n\r\nljksdhsdljhljhjsdhsdhsdhdsdskjhdskjh\r\n\r\n\r\nsdkjhsdkjhjskhkjhkjsdhks\r\n\r\njkhkjhgkjhg jkhjkhjkhjk hjkhj hj hjkh jkhjhjk hj hjhj hjh hjh jh jmj mjml jmljjghi gjhgfhg\r\n\r\nljksdhsdljhljhjsdhsdhsdhdsdskjhdskjh\r\n\r\n\r\nsdkjhsdkjhjskhkjhkjsdhks\r\n\r\njkhkjhgkjhg jkhjkhjkhjk hjkhj hj hjkh jkhjhjk hj hjhj hjh hjh jh jmj mjml jmljjghi gjhgfhg\r\n\r\nljksdhsdljhljhjsdhsdhsdhdsdskjhdskjh\r\n\r\n\r\nsdkjhsdkjhjskhkjhkjsdhks\r\n\r\njkhkjhgkjhg jkhjkhjkhjk hjkhj hj hjkh jkhjhjk hj hjhj hjh hjh jh jmj mjml jmljjghi gjhgfhg\r\n\r\nljksdhsdljhljhjsdhsdhsdhdsdskjhdskjh\r\n\r\n\r\nsdkjhsdkjhjskhkjhkjsdhks\r\n\r\njkhkjhgkjhg jkhjkhjkhjk hjkhj hj hjkh jkhjhjk hj hjhj hjh hjh jh jmj mjml jmljjghi gjhgfhg\r\n\r\nljksdhsdljhljhjsdhsdhsdhdsdskjhdskjh\r\n\r\n\r\nsdkjhsdkjhjskhkjhkjsdhks\r\n\r\njkhkjhgkjhg jkhjkhjkhjk hjkhj hj hjkh jkhjhjk hj hjhj hjh hjh jh jmj mjml jmljjghi gjhgfhg\r\n\r\nljksdhsdljhljhjsdhsdhsdhdsdskjhdskjh\r\n\r\n\r\nsdkjhsdkjhjskhkjhkjsdhks\r\n\r\njkhkjhgkjhg jkhjkhjkhjk hjkhj hj hjkh jkhjhjk hj hjhj hjh hjh jh jmj mjml jmljjghi gjhgfhg\r\n\r\nljksdhsdljhljhjsdhsdhsdhdsdskjhdskjh\r\n\r\n\r\nsdkjhsdkjhjskhkjhkjsdhks\r\n\r\njkhkjhgkjhg jkhjkhjkhjk hjkhj hj hjkh jkhjhjk hj hjhj hjh hjh jh jmj mjml jmljjghi gjhgfhg\r\n\r\nljksdhsdljhljhjsdhsdhsdhdsdskjhdskjh\r\n\r\n\r\nsdkjhsdkjhjskhkjhkjsdhks\r\n\r\njkhkjhgkjhg jkhjkhjkhjk hjkhj hj hjkh jkhjhjk hj hjhj hjh hjh jh jmj mjml jmljjghi gjhgfhg\r\n\r\nljksdhsdljhljhjsdhsdhsdhdsdskjhdskjh\r\n\r\n\r\nsdkjhsdkjhjskhkjhkjsdhks\r\n\r\njkhkjhgkjhg jkhjkhjkhjk hjkhj hj hjkh jkhjhjk hj hjhj hjh hjh jh jmj mjml jmljjghi gjhgfhg\r\n\r\nljksdhsdljhljhjsdhsdhsdhdsdskjhdskjh\r\n\r\n\r\nsdkjhsdkjhjskhkjhkjsdhks',
        '2022-10-13 12:26:31',
        0,
        0
    ),
    (
        28,
        2,
        1,
        'hgjhfgjkfdgjhd hjhj hhdgj hdjhfg h',
        '2022-10-13 12:26:15',
        0,
        0
    ),
    (
        29,
        1,
        1,
        'nkjkjjkjkjk',
        '2022-10-13 11:47:49',
        0,
        0
    ),
    (
        30,
        1,
        1,
        'C\'est moi ça va ?',
        '2022-10-13 10:36:14',
        0,
        0
    ),
    (
        31,
        2,
        1,
        'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam dictum turpis at fringilla consectetur. Sed eget sem diam. Phasellus sit amet lobortis sem. Integer vitae porta massa. Praesent commodo lectus ac eleifend porttitor. Nam non fringilla nibh. Nam condimentum scelerisque ipsum, ac hendrerit neque egestas sed.\r\n\r\nOrci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Etiam faucibus libero at metus consequat iaculis. Quisque interdum porttitor erat vel lobortis. Phasellus venenatis urna elit, at congue libero varius quis. Nam vestibulum urna gravida, condimentum dui sed, scelerisque risus. Duis sed ante vel odio vulputate rutrum ac et arcu. Sed tincidunt orci diam, tempor faucibus ligula vulputate vulputate. Pellentesque varius eget felis sit amet sodales. Maecenas nunc purus, congue vitae scelerisque at, ultricies at quam. Mauris pulvinar lacus a erat maximus fringilla. Sed iaculis magna a sollicitudin dapibus. Duis ac tempor lorem, vel semper nunc. Donec nisl mauris, sagittis ac purus non, volutpat volutpat erat. Nunc nisl sem, lacinia id ante in, efficitur dignissim est. Maecenas malesuada, est at auctor condimentum, nisi turpis efficitur tortor, vel dictum turpis nisi in tellus.\r\n\r\nIn hendrerit massa vitae egestas faucibus. Quisque sed elementum lectus. Aenean fermentum felis velit, et imperdiet urna molestie sed. Praesent sed pharetra tellus, vitae euismod arcu. Proin imperdiet eros ac ipsum laoreet eleifend. Proin molestie ultrices quam nec commodo. Ut dictum lobortis nisl, sit amet consequat sem auctor id. Integer tincidunt hendrerit erat, at rutrum lectus porttitor id.\r\n\r\nCras lectus tellus, commodo et ultrices eu, egestas ac nunc. Vivamus auctor in magna in rutrum. Mauris at orci eu nisi egestas vulputate. Aenean non purus ante. Phasellus sollicitudin tristique diam, quis tempus lorem placerat et. Vivamus at dolor ipsum. Integer eget hendrerit risus, eget consectetur nisi. Nunc leo quam, tincidunt non justo id, interdum tempus arcu. Nulla vitae accumsan erat. Nunc euismod leo sed sapien convallis, eu volutpat nulla aliquam. Quisque facilisis sollicitudin felis eget mollis. Cras laoreet placerat commodo. Etiam egestas diam vel nulla luctus, vel euismod diam dictum. Morbi eleifend nibh a est tincidunt ultricies. Maecenas vel malesuada metus.\r\n\r\nCurabitur porttitor diam at nulla luctus, id luctus nunc pellentesque. Aliquam erat volutpat. Suspendisse posuere sem libero, ut dignissim ex convallis sagittis. Suspendisse placerat tortor nibh, non pellentesque nibh viverra nec. Cras vestibulum ac nulla sed dictum. Nulla facilisi. Integer at sapien porta, mattis justo in, luctus augue. Integer vitae tristique elit. Nullam congue mauris quis arcu iaculis, sit amet fermentum magna faucibus. Phasellus elementum nisi quis dui venenatis ornare. Nulla eleifend lectus a commodo maximus. Interdum et malesuada fames ac ante ipsum primis in faucibus. Donec gravida, enim a lacinia rhoncus, metus dolor pellentesque leo, eu facilisis orci metus et eros. Nunc elementum sollicitudin suscipit. Fusce in lorem dui. Aliquam sit amet augue faucibus, hendrerit tellus vestibulum, dapibus lacus.',
        '2022-10-13 13:07:05',
        0,
        0
    ),
    (
        32,
        1,
        2,
        'jkhkjhgkjhg jkhjkhjkhjk hjkhj hj hjkh jkhjhjk hj hjhj hjh hjh jh jmj mjml jmljjghi gjhgfhg\r\n\r\nljksdhsdljhljhjsdhsdhsdhdsdskjhdskjh\r\n\r\n\r\nsdkjhsdkjhjskhkjhkjsdhks\r\n\r\njkhkjhgkjhg jkhjkhjkhjk hjkhj hj hjkh jkhjhjk hj hjhj hjh hjh jh jmj mjml jmljjghi gjhgfhg\r\n\r\nljksdhsdljhljhjsdhsdhsdhdsdskjhdskjh\r\n\r\n\r\nsdkjhsdkjhjskhkjhkjsdhks\r\n\r\njkhkjhgkjhg jkhjkhjkhjk hjkhj hj hjkh jkhjhjk hj hjhj hjh hjh jh jmj mjml jmljjghi gjhgfhg\r\n\r\nljksdhsdljhljhjsdhsdhsdhdsdskjhdskjh\r\n\r\n\r\nsdkjhsdkjhjskhkjhkjsdhks\r\n\r\njkhkjhgkjhg jkhjkhjkhjk hjkhj hj hjkh jkhjhjk hj hjhj hjh hjh jh jmj mjml jmljjghi gjhgfhg\r\n\r\nljksdhsdljhljhjsdhsdhsdhdsdskjhdskjh\r\n\r\n\r\nsdkjhsdkjhjskhkjhkjsdhks\r\n\r\njkhkjhgkjhg jkhjkhjkhjk hjkhj hj hjkh jkhjhjk hj hjhj hjh hjh jh jmj mjml jmljjghi gjhgfhg\r\n\r\nljksdhsdljhljhjsdhsdhsdhdsdskjhdskjh\r\n\r\n\r\nsdkjhsdkjhjskhkjhkjsdhks\r\n\r\njkhkjhgkjhg jkhjkhjkhjk hjkhj hj hjkh jkhjhjk hj hjhj hjh hjh jh jmj mjml jmljjghi gjhgfhg\r\n\r\nljksdhsdljhljhjsdhsdhsdhdsdskjhdskjh\r\n\r\n\r\nsdkjhsdkjhjskhkjhkjsdhks\r\n\r\njkhkjhgkjhg jkhjkhjkhjk hjkhj hj hjkh jkhjhjk hj hjhj hjh hjh jh jmj mjml jmljjghi gjhgfhg\r\n\r\nljksdhsdljhljhjsdhsdhsdhdsdskjhdskjh\r\n\r\n\r\nsdkjhsdkjhjskhkjhkjsdhks\r\n\r\njkhkjhgkjhg jkhjkhjkhjk hjkhj hj hjkh jkhjhjk hj hjhj hjh hjh jh jmj mjml jmljjghi gjhgfhg\r\n\r\nljksdhsdljhljhjsdhsdhsdhdsdskjhdskjh\r\n\r\n\r\nsdkjhsdkjhjskhkjhkjsdhks\r\n\r\njkhkjhgkjhg jkhjkhjkhjk hjkhj hj hjkh jkhjhjk hj hjhj hjh hjh jh jmj mjml jmljjghi gjhgfhg\r\n\r\nljksdhsdljhljhjsdhsdhsdhdsdskjhdskjh\r\n\r\n\r\nsdkjhsdkjhjskhkjhkjsdhks\r\n\r\njkhkjhgkjhg jkhjkhjkhjk hjkhj hj hjkh jkhjhjk hj hjhj hjh hjh jh jmj mjml jmljjghi gjhgfhg\r\n\r\nljksdhsdljhljhjsdhsdhsdhdsdskjhdskjh\r\n\r\n\r\nsdkjhsdkjhjskhkjhkjsdhks',
        '2022-10-13 12:26:31',
        0,
        0
    ),
    (
        33,
        2,
        1,
        'hgjhfgjkfdgjhd hjhj hhdgj hdjhfg h',
        '2022-10-13 12:26:15',
        0,
        0
    ),
    (
        34,
        1,
        1,
        'nkjkjjkjkjk',
        '2022-10-13 11:47:49',
        0,
        0
    ),
    (
        35,
        1,
        1,
        'C\'est moi ça va ?',
        '2022-10-13 10:36:14',
        0,
        0
    ),
    (
        36,
        3,
        1,
        'sfddsfdsdfdsdsdsfdsdsf',
        '2022-10-13 13:44:34',
        0,
        0
    ),
    (
        37,
        1,
        3,
        'Test',
        '2022-10-13 13:49:21',
        1,
        0
    );

-- --------------------------------------------------------

--
-- Structure de la table `moyens_paiement`
--

CREATE TABLE `moyens_paiement` (
    `id` int(11) NOT NULL,
    `nom` varchar(30) NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = latin1 COLLATE = latin1_swedish_ci;

--
-- Déchargement des données de la table `moyens_paiement`
--

INSERT INTO
    `moyens_paiement` (`id`, `nom`)
VALUES (1, 'Carte Bancaire');

-- --------------------------------------------------------

--
-- Structure de la table `pages`
--

CREATE TABLE `pages` (
    `id` int(11) NOT NULL,
    `nom` varchar(255) NOT NULL,
    `mod` varchar(100) NOT NULL,
    `url` varchar(100) NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = latin1 COLLATE = latin1_swedish_ci;

--
-- Déchargement des données de la table `pages`
--

INSERT INTO
    `pages` (`id`, `nom`, `mod`, `url`)
VALUES (
        1,
        'Accueil',
        'home',
        'views/home.php'
    ),
    (
        2,
        'Inscription',
        'inscription',
        'views/register.php'
    ),
    (
        3,
        'Se connecter',
        'connexion',
        'views/login.php'
    ),
    (
        4,
        'A propos',
        'aa-propos',
        'views/aa-propos.php'
    ),
    (
        5,
        'FAQ',
        'faq',
        'views/faq.php'
    ),
    (
        6,
        'Liste des restaurants',
        'liste-restaurants',
        'views/liste-restaurants.php'
    ),
    (
        7,
        'Contact',
        'contact',
        'views/contact.php'
    ),
    (
        8,
        'Mon compte',
        'mon-compte',
        'views/mon-compte.php'
    ),
    (
        9,
        'Commande',
        'commande',
        'views/commande.php'
    ),
    (
        10,
        'Profil',
        'profil',
        'views/profile.php'
    ),
    (
        11,
        'Mon compte restaurateur',
        'mon-compte-restaurateur',
        'views/mon-compte-restaurateur.php'
    );

-- --------------------------------------------------------

--
-- Structure de la table `profils`
--

CREATE TABLE `profils` (
    `id` int(11) NOT NULL,
    `libelle` varchar(50) NOT NULL,
    `type` varchar(50) NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = latin1 COLLATE = latin1_swedish_ci;

--
-- Déchargement des données de la table `profils`
--

INSERT INTO
    `profils` (`id`, `libelle`, `type`)
VALUES (
        1,
        'Administrateurs',
        'administrateurs'
    ),
    (
        2,
        'Restaurants',
        'restaurateurs'
    ),
    (
        3,
        'Clients de restaurants',
        'clients'
    );

-- --------------------------------------------------------

--
-- Structure de la table `promos`
--

CREATE TABLE `promos` (
    `id` int(11) NOT NULL,
    `code` text NOT NULL,
    `percent` int(11) NOT NULL,
    `actif` int(11) NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = latin1 COLLATE = latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Structure de la table `restaurants`
--

CREATE TABLE `restaurants` (
    `id` int(11) NOT NULL,
    `name` varchar(150) NOT NULL,
    `slug` varchar(150) NOT NULL,
    `description` text DEFAULT NULL,
    `city` varchar(100) DEFAULT NULL,
    `main_image` varchar(255) DEFAULT 'default-resto.jpg',
    `category_id` int(11) DEFAULT NULL,
    `rating` decimal(2, 1) DEFAULT 0.0,
    `review_count` int(11) DEFAULT 0,
    `is_featured` tinyint(1) DEFAULT 0,
    `subscription_active` tinyint(1) DEFAULT 0,
    `created_at` timestamp NULL DEFAULT current_timestamp(),
    `id_restaurateur` int(11) NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = latin1 COLLATE = latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Structure de la table `restaurateurs`
--

CREATE TABLE `restaurateurs` (
    `id` int(11) NOT NULL,
    `nom` varchar(100) DEFAULT NULL,
    `prenom` varchar(100) DEFAULT NULL,
    `email` varchar(150) DEFAULT NULL,
    `telephone` varchar(20) DEFAULT NULL,
    `dateinscription` timestamp NULL DEFAULT current_timestamp()
) ENGINE = InnoDB DEFAULT CHARSET = latin1 COLLATE = latin1_swedish_ci;

--
-- Déchargement des données de la table `restaurateurs`
--

INSERT INTO
    `restaurateurs` (
        `id`,
        `nom`,
        `prenom`,
        `email`,
        `telephone`,
        `dateinscription`
    )
VALUES (
        1,
        'Goguet-galli',
        'Yann',
        'yann@youonline.fr',
        '0624997171',
        '2026-03-25 14:45:37'
    );

-- --------------------------------------------------------

--
-- Structure de la table `status`
--

CREATE TABLE `status` (
    `id` int(11) NOT NULL,
    `libelle` varchar(100) NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = latin1 COLLATE = latin1_swedish_ci;

--
-- Déchargement des données de la table `status`
--

INSERT INTO
    `status` (`id`, `libelle`)
VALUES (1, 'Paiement en attente'),
    (2, 'Paiement accepté'),
    (
        3,
        'Commande en cours de préparation'
    ),
    (4, 'Commande finalisée'),
    (5, 'Commande annulée');

-- --------------------------------------------------------

--
-- Structure de la table `user_logs`
--

CREATE TABLE `user_logs` (
    `id` int(11) NOT NULL,
    `user_id` int(11) NOT NULL,
    `action_type` varchar(50) NOT NULL,
    `message` varchar(255) NOT NULL,
    `ip_address` varchar(45) DEFAULT NULL,
    `created_at` datetime DEFAULT current_timestamp()
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

--
-- Déchargement des données de la table `user_logs`
--

INSERT INTO
    `user_logs` (
        `id`,
        `user_id`,
        `action_type`,
        `message`,
        `ip_address`,
        `created_at`
    )
VALUES (
        1,
        1,
        'create_user',
        'Création du compte pour : yann@youonline.fr',
        '82.67.181.254',
        '2026-03-25 15:45:37'
    ),
    (
        2,
        1,
        'login',
        'Connexion au site réussie',
        '82.67.181.254',
        '2026-03-25 15:47:16'
    ),
    (
        3,
        1,
        'login',
        'Connexion au site réussie',
        '82.67.181.254',
        '2026-03-25 15:47:20'
    ),
    (
        4,
        1,
        'login',
        'Connexion au site réussie',
        '82.67.181.254',
        '2026-03-25 15:51:05'
    ),
    (
        5,
        1,
        'login',
        'Connexion au site réussie',
        '82.67.181.254',
        '2026-03-25 15:52:32'
    ),
    (
        6,
        1,
        'login',
        'Connexion au site réussie',
        '82.67.181.254',
        '2026-03-25 15:52:33'
    ),
    (
        7,
        1,
        'login',
        'Connexion au site réussie',
        '82.67.181.254',
        '2026-03-25 15:55:36'
    ),
    (
        8,
        1,
        'login',
        'Connexion au site réussie',
        '82.67.181.254',
        '2026-03-25 15:56:16'
    ),
    (
        9,
        1,
        'login',
        'Connexion au site réussie',
        '82.67.181.254',
        '2026-03-25 16:02:34'
    );

-- --------------------------------------------------------

--
-- Structure de la table `utilisateurs`
--

CREATE TABLE `utilisateurs` (
    `id` int(11) NOT NULL,
    `email` varchar(250) NOT NULL,
    `motdepasse` varchar(100) NOT NULL,
    `profil` int(11) NOT NULL DEFAULT 3,
    `profil_id` int(11) DEFAULT NULL,
    `dateinscription` datetime DEFAULT NULL,
    `dateconnect` datetime DEFAULT NULL,
    `dateaction` datetime DEFAULT NULL,
    `token` varchar(50) DEFAULT NULL,
    `actif` tinyint(1) NOT NULL DEFAULT 0
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb3 COLLATE = utf8mb3_general_ci;

--
-- Déchargement des données de la table `utilisateurs`
--

INSERT INTO
    `utilisateurs` (
        `id`,
        `email`,
        `motdepasse`,
        `profil`,
        `profil_id`,
        `dateinscription`,
        `dateconnect`,
        `dateaction`,
        `token`,
        `actif`
    )
VALUES (
        1,
        'yann@youonline.fr',
        '$2y$09$.OKh6fq9CmLxs8YdTBqCd.xv/6vr2CAE1Gt0CNBlUomq7lSmYfd6u',
        2,
        1,
        '2026-03-25 15:45:37',
        '2026-03-25 16:02:34',
        NULL,
        '',
        1
    );

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `administrateurs`
--
ALTER TABLE `administrateurs` ADD PRIMARY KEY (`id`);

--
-- Index pour la table `autorisations`
--
ALTER TABLE `autorisations` ADD PRIMARY KEY (`id`);

--
-- Index pour la table `categories`
--
ALTER TABLE `categories` ADD PRIMARY KEY (`id`);

--
-- Index pour la table `civilites`
--
ALTER TABLE `civilites` ADD UNIQUE KEY `id` (`id`);

--
-- Index pour la table `clients`
--
ALTER TABLE `clients` ADD PRIMARY KEY (`id`);

--
-- Index pour la table `configuration`
--
ALTER TABLE `configuration` ADD PRIMARY KEY (`id`);

--
-- Index pour la table `langues`
--
ALTER TABLE `langues` ADD PRIMARY KEY (`id`);

--
-- Index pour la table `messages`
--
ALTER TABLE `messages` ADD PRIMARY KEY (`id`);

--
-- Index pour la table `moyens_paiement`
--
ALTER TABLE `moyens_paiement` ADD PRIMARY KEY (`id`);

--
-- Index pour la table `pages`
--
ALTER TABLE `pages` ADD PRIMARY KEY (`id`);

--
-- Index pour la table `profils`
--
ALTER TABLE `profils` ADD PRIMARY KEY (`id`);

--
-- Index pour la table `promos`
--
ALTER TABLE `promos` ADD PRIMARY KEY (`id`);

--
-- Index pour la table `restaurants`
--
ALTER TABLE `restaurants`
ADD PRIMARY KEY (`id`),
ADD UNIQUE KEY `slug` (`slug`),
ADD KEY `category_id` (`category_id`);

--
-- Index pour la table `restaurateurs`
--
ALTER TABLE `restaurateurs`
ADD PRIMARY KEY (`id`),
ADD UNIQUE KEY `email` (`email`);

--
-- Index pour la table `status`
--
ALTER TABLE `status` ADD PRIMARY KEY (`id`);

--
-- Index pour la table `user_logs`
--
ALTER TABLE `user_logs`
ADD PRIMARY KEY (`id`),
ADD KEY `idx_user_log` (`user_id`),
ADD KEY `idx_date_log` (`created_at`);

--
-- Index pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
ADD PRIMARY KEY (`id`),
ADD UNIQUE KEY `id` (`id`),
ADD KEY `id_2` (`id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `administrateurs`
--
ALTER TABLE `administrateurs`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `autorisations`
--
ALTER TABLE `autorisations`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,
AUTO_INCREMENT = 6;

--
-- AUTO_INCREMENT pour la table `categories`
--
ALTER TABLE `categories`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `civilites`
--
ALTER TABLE `civilites`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,
AUTO_INCREMENT = 4;

--
-- AUTO_INCREMENT pour la table `clients`
--
ALTER TABLE `clients` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `configuration`
--
ALTER TABLE `configuration`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `langues`
--
ALTER TABLE `langues`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,
AUTO_INCREMENT = 3;

--
-- AUTO_INCREMENT pour la table `messages`
--
ALTER TABLE `messages`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,
AUTO_INCREMENT = 38;

--
-- AUTO_INCREMENT pour la table `moyens_paiement`
--
ALTER TABLE `moyens_paiement`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,
AUTO_INCREMENT = 2;

--
-- AUTO_INCREMENT pour la table `pages`
--
ALTER TABLE `pages`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,
AUTO_INCREMENT = 12;

--
-- AUTO_INCREMENT pour la table `profils`
--
ALTER TABLE `profils`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,
AUTO_INCREMENT = 4;

--
-- AUTO_INCREMENT pour la table `promos`
--
ALTER TABLE `promos` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `restaurants`
--
ALTER TABLE `restaurants`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `restaurateurs`
--
ALTER TABLE `restaurateurs`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,
AUTO_INCREMENT = 2;

--
-- AUTO_INCREMENT pour la table `status`
--
ALTER TABLE `status`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,
AUTO_INCREMENT = 6;

--
-- AUTO_INCREMENT pour la table `user_logs`
--
ALTER TABLE `user_logs`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,
AUTO_INCREMENT = 10;

--
-- AUTO_INCREMENT pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,
AUTO_INCREMENT = 2;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `restaurants`
--
ALTER TABLE `restaurants`
ADD CONSTRAINT `restaurants_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`);

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */
;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */
;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */
;

INSERT INTO `categories` (`name`, `icon`) VALUES
('Français', 'fa-wine-glass'),
('Italien', 'fa-pizza-slice'),
('Japonais', 'fa-fish'),
('Chinois', 'fa-bowl-rice'),
('Indien', 'fa-pepper-hot'),
('Mexicain', 'fa-pepper-hot'),
('Américain', 'fa-burger'),
('Libanais', 'fa-leaf'),
('Thaïlandais', 'fa-shrimp'),
('Pizzeria', 'fa-pizza-slice'),
('Fast-food', 'fa-burger'),
('Brasserie', 'fa-beer-mug-empty'),
('Gastro', 'fa-star'),
('Végétarien', 'fa-carrot'),
('Fruits de mer', 'fa-shrimp');

USE Miamy;

INSERT INTO pages (nom, `mod`, url) VALUES 
('Accueil', 'accueil', 'views/home.php'),
('À propos', 'a-propos', 'views/a-propos.php'),
('FAQ', 'faq', 'views/faq.php'),
('Contact', 'contact', 'views/contact.php'),
('Restaurants', 'restaurants', 'views/restaurants.php'),
('Connexion', 'connexion', 'views/login.php'),
('Inscription', 'inscription', 'views/register.php'),
('Mon compte restaurateur', 'mon-compte-restaurateur', 'views/mon-compte-restaurateur.php'),
('Déconnexion', 'deconnexion', 'views/deconnexion.php'),
('Ajouter un restaurant', 'ajouter-restaurant', 'views/ajouter-restaurant.php'),
('Modifier un restaurant', 'modifier-restaurant', 'views/modifier-restaurant.php'),
('Gestion de la carte',  'gestion-carte',  'views/gestion-carte.php'),
('Ajouter un plat',      'ajouter-plat',   'views/ajouter-plat.php'),
('Modifier un plat',     'modifier-plat',  'views/modifier-plat.php'),
('Supprimer un plat',    'supprimer-plat', 'views/supprimer-plat.php');
