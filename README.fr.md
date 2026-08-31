# Miamy

*[English version available here](README.md)*

Miamy est une plateforme de commande de repas en ligne, un peu dans l'esprit d'Uber Eats, que j'ai développée seul lors d'un stage chez YOUONLINE. Elle comporte trois espaces distincts, un pour les clients qui parcourent les restaurants et passent commande, un pour les restaurateurs qui gèrent leur carte et leurs horaires, et un pour les administrateurs qui supervisent l'ensemble de la plateforme.

## Ce que le site propose

Les clients peuvent parcourir la liste des restaurants, consulter la carte et les informations d'un restaurant, et passer commande. Les restaurateurs disposent de leur propre tableau de bord pour gérer leurs plats (ajout, modification, suppression, bascule disponible / indisponible), les réorganiser par glisser déposer au sein d'une catégorie, et définir leurs horaires d'ouverture jour par jour. Les administrateurs peuvent gérer les restaurants et les utilisateurs de la plateforme, et chaque action sensible (changement de rôle, suppression) est enregistrée dans un journal pour garder une trace de qui a fait quoi.

L'écran de gestion des plats est la partie dont je suis le plus fier : la réorganisation par glisser déposer et la bascule de disponibilité passent toutes les deux par des appels AJAX, la page ne se recharge donc jamais, et le nouvel ordre est enregistré directement en base.

## Comment c'est construit

Il n'y a pas de framework, tout est en PHP fait à la main, organisé autour d'un front controller. `index.php` reçoit chaque requête, cherche la page demandée dans une table de routage qui associe un nom de page à un contrôleur et une méthode, puis délègue le traitement à celui ci. La logique métier est répartie dans un ensemble de classes (`classes/class.users.php`, `class.restaurants.php`, `class.plats.php`, etc.), la connexion à la base est un singleton PDO qui aligne au passage le fuseau horaire de MySQL sur Europe/Paris pour que les horodatages soient corrects, et chaque action sensible vérifie à la fois le rôle de l'utilisateur et le fait qu'il soit bien propriétaire de la ressource qu'il modifie, un restaurateur ne peut par exemple modifier que ses propres plats. Les mots de passe sont hachés en bcrypt, et les sorties sont échappées avec `htmlspecialchars` pour éviter les failles XSS.

**Stack :** PHP (POO, sans framework), MySQL / PDO, JavaScript (Fetch / AJAX), SortableJS, Bootstrap, HTML / CSS.

## Installation en local

Il faut PHP, MySQL et un serveur local (j'utilise XAMPP). Clone le dépôt, importe `Miamy.sql` dans MySQL pour créer le schéma, puis copie `.env.example` en `.env` et renseigne l'hôte, l'utilisateur, le mot de passe et le nom de ta base en local, ainsi qu'une valeur pour `BASE_SALT`. Place le projet dans la racine web de ton serveur et ouvre `index.php` dans le navigateur, l'application détecte automatiquement si elle tourne en local ou en production et ajuste son URL de base en conséquence.

## À propos de moi

Je suis Maxime Paulin, développeur web fraîchement titulaire du titre DWWM, et je poursuis actuellement une formation CDA (Concepteur Développeur d'Applications) à l'école CESI. Je suis à la recherche d'une alternance pour l'année prochaine. Le site n'est plus hébergé en ligne, cet hébergement étant lié à la durée du stage, mais le code source complet est disponible ici.

[LinkedIn](https://www.linkedin.com/in/maxime-paulin-968ab1266/) · [GitHub](https://github.com/meagle-pixel)
