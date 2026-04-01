# GESTION DES URLS - PROJET MIAMY

## PROBLÈME INITIAL

Le site fonctionne en production sur miamy.fr mais pas en local car les URLs
ne pointent pas au bon endroit.

- En PROD : https://miamy.fr/connexion ✅
- En LOCAL : http://localhost/Miamy/connexion ✅
  http://localhost/connexion ❌ (erreur 404)

## SOLUTION MISE EN PLACE

1. ## CONFIGURATION DYNAMIQUE (config.php)

   Le fichier config.php détecte automatiquement l'environnement :

   ```php
   if ($_SERVER['HTTP_HOST'] == 'miamy.local' || $_SERVER['HTTP_HOST'] == 'localhost') {
       // LOCAL
       $GLOBALS["url"] = 'http://localhost/Miamy';
   } else {
       // PRODUCTION
       $GLOBALS["url"] = 'https://miamy.fr';
   }
   ```

2. ## BALISE <base> (views/partials/head.php)

   Ajouter après <head> :

   ```html
   <head>
     <base href="<?= $GLOBALS['url'] ?>/" />
   </head>
   ```

   Cette balise indique au navigateur l'URL de base pour tous les liens.
   Tous les liens RELATIFS (sans / au début) utiliseront cette base.

3. ## LIENS HTML (dans toutes les vues)

   AVANT (ne marche pas en local) :

   ```html
   <a href="/connexion">Connexion</a>
   <form action="/inscription" method="POST"></form>
   ```

   APRÈS (marche partout) :

   ```html
   <a href="connexion">Connexion</a>
   <form action="inscription" method="POST"></form>
   ```

   RÈGLE : Jamais de "/" au début des liens HTML.

4. ## REDIRECTIONS PHP (dans les fichiers PHP)

   La balise <base> ne fonctionne que pour le HTML, pas pour les
   redirections en PHP/JavaScript.

   AVANT :

   ```php
   echo "<script>window.location.href='/mon-compte-restaurateur';</script>";
   ```

   APRÈS :

   ```php
   echo "<script>window.location.href='" . $GLOBALS['url'] . "/mon-compte-restaurateur';</script>";
   ```

   RÈGLE : Toujours utiliser $GLOBALS['url'] pour les redirections PHP.

## RÉSUMÉ DES 3 RÈGLES

┌─────────────────────────────────────────────────────────────────────────┐
│ 1. <base href="<?= $GLOBALS['url'] ?>/"> dans head.php │
│ 2. Liens HTML : jamais de "/" au début → href="connexion" │
│ 3. Redirections PHP : toujours $GLOBALS['url'] . "/page" │
└─────────────────────────────────────────────────────────────────────────┘

## RECHERCHER/REMPLACER DANS VS CODE

Pour corriger tous les liens d'un coup :

1. Ouvrir VS Code
2. Ctrl + Shift + H (recherche globale)
3. Cliquer sur "..." et dans "Fichiers à inclure" mettre : ./www/Miamy/views
4. Premier remplacement :
   - Recherche : href="/
   - Remplace : href="
5. Deuxième remplacement :
   - Recherche : action="/
   - Remplace : action="

---

   # HTACCESS - RÉÉCRITURE D'URL

## OBJECTIF

Transformer les URLs moches en URLs propres :
❌ miamy.fr/index.php?mod=connexion
✅ miamy.fr/connexion

## EXPLICATION LIGNE PAR LIGNE

1. RewriteEngine On → Active la réécriture d'URL
2. RewriteCond ... !-f → Si ce n'est PAS un fichier existant
3. RewriteCond ... !-d → Si ce n'est PAS un dossier existant
4. RewriteRule ... → Redirige vers index.php?mod=xxx

## EXEMPLE

Utilisateur tape : miamy.fr/connexion
↓
Apache vérifie : "connexion" est un fichier ? NON
"connexion" est un dossier ? NON
↓
Apache redirige : index.php?mod=connexion
↓
PHP reçoit : $\_GET['mod'] = 'connexion'


FONCTIONNALITÉS DÉVELOPPÉES
-----------------------------
1. INSCRIPTION (views/register.php)
   → Crée un restaurateur + utilisateur
 
2. CONNEXION (views/login.php)
   → Authentification + sessions
 
3. DÉCONNEXION (views/deconnexion.php)
   → Destruction session + redirection
 
4. HEADER DYNAMIQUE (views/partials/header.php)
   → Affiche "Bonjour [Prénom]" si connecté
   → Affiche "Connexion | Inscription" sinon
 
5. DASHBOARD RESTAURATEUR (views/mon-compte-restaurateur.php)
   → Liste des restaurants du gérant
   → Boutons : Gérer carte, QR Codes, Modifier
 
6. AJOUTER RESTAURANT (views/ajouter-restaurant.php)
   → Formulaire : nom, ville, catégorie, description, image
   → Upload image vers assets/img/restaurants/
 
7. MODIFIER RESTAURANT (views/modifier-restaurant.php)
   → Pré-remplissage du formulaire
   → Vérification propriétaire
   → Update en BDD
 
 
TABLES BDD UTILISÉES
-----------------------------
- utilisateurs : comptes de connexion
- restaurateurs : infos profil gérant
- restaurants : établissements
- categories : types de cuisine (Français, Italien, etc.)
- pages : routage URL → fichier PHP
