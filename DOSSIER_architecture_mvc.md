# Architecture MVC et Front Controller

## 1. Présentation de l'architecture

Le projet Miamy est structuré selon le pattern **MVC** (Modèle-Vue-Contrôleur), une organisation standard dans le développement web qui consiste à séparer trois responsabilités distinctes :

- Les **Modèles** (`classes/`) contiennent les classes qui dialoguent avec la base de données (`User`, `Restaurant`, `Plat`, etc.). Ce sont les seules classes qui contiennent des requêtes SQL.
- Les **Vues** (`views/`) contiennent les fichiers PHP qui produisent le HTML envoyé au navigateur. Elles ne contiennent ni logique métier, ni requête SQL.
- Les **Contrôleurs** (`controllers/`) font le lien entre les deux : ils reçoivent la requête HTTP, appellent les modèles pour récupérer les données, et préparent le tableau de variables à transmettre à la vue.

Cette séparation rend le code beaucoup plus **lisible et maintenable** : si je veux modifier un affichage, je vais dans `views/`, sans risquer de toucher à la logique métier ou aux requêtes SQL.

---

## 2. La structure des dossiers

```
Miamy/
│
├── index.php                         ← Front controller (point d'entrée unique)
├── functions.php                     ← Bootstrap : session_start(), includes, timezone
├── config.php                        ← Configuration BDD selon environnement (dev/prod)
├── .env                              ← Variables sensibles (BDD, sel global) — non versionné
├── .env.example                      ← Modèle de .env pour le déploiement
├── .htaccess                         ← Réécriture d'URL Apache
├── .gitignore                        ← Fichiers exclus du dépôt Git
├── Miamy.sql                         ← Export de la structure de la base de données
├── README.md                         ← Documentation du projet
├── robots.txt                        ← Directives pour les moteurs de recherche
│
├── classes/                          ← MODÈLES — uniquement du SQL et de la logique métier
│   ├── class.database.php            ← Singleton PDO
│   ├── class.users.php               ← Comptes utilisateurs + authentification
│   ├── class.restaurateurs.php       ← Profils restaurateurs
│   ├── class.clients.php             ← Profils clients
│   ├── class.restaurants.php         ← Restaurants
│   ├── class.plats.php               ← Plats de la carte
│   ├── class.category.php            ← Catégories de plats
│   ├── class.horaires.php            ← Horaires d'ouverture
│   ├── class.pages.php               ← Table de routage (slug → vue + titre)
│   ├── class.userlogs.php            ← Journalisation des actions utilisateur
│   ├── class.imageuploader.php       ← Helper d'upload d'images centralisé
│   └── class.functions.php           ← Fonctions utilitaires globales
│
├── controllers/                      ← CONTRÔLEURS — orchestrent modèles + vues
│   ├── HomeController.php            ← Page d'accueil
│   ├── AuthController.php            ← Connexion, inscription, déconnexion
│   ├── UserController.php            ← Profil utilisateur (édition, comptes)
│   ├── RestaurantController.php      ← CRUD restaurants + horaires
│   ├── PlatController.php            ← CRUD plats + endpoints AJAX
│   └── AdminController.php           ← Espace administrateur
│
├── views/                            ← VUES — uniquement du HTML/affichage
│   ├── home.php                      ← Accueil
│   ├── login.php                     ← Formulaire de connexion
│   ├── register.php                  ← Inscription restaurateur
│   ├── register-client.php           ← Inscription client
│   ├── forgot-password.php           ← Mot de passe oublié
│   ├── mon-compte.php                ← Espace client
│   ├── mon-compte-restaurateur.php   ← Espace restaurateur
│   ├── profil-editer.php             ← Édition du profil
│   ├── profile.php                   ← Profil public
│   ├── menu-compte.php               ← Menu latéral des comptes
│   ├── liste-restaurants.php         ← Liste publique des restaurants
│   ├── ajouter-restaurant.php        ← Création de restaurant
│   ├── modifier-restaurant.php       ← Édition de restaurant
│   ├── supprimer-restaurant.php      ← Confirmation de suppression
│   ├── details.php                   ← Détails d'un restaurant + horaires
│   ├── liste-plats.php               ← Carte côté client
│   ├── gestion-carte.php             ← Gestion de la carte côté restaurateur
│   ├── ajouter-plat.php              ← Création de plat
│   ├── modifier-plat.php             ← Édition de plat
│   ├── supprimer-plat.php            ← Confirmation de suppression
│   ├── commande.php                  ← Commande en cours
│   ├── commandes.php                 ← Historique des commandes
│   ├── contact.php                   ← Formulaire de contact
│   ├── a-propos.php                  ← Page "À propos"
│   ├── faq.php                       ← Foire aux questions
│   ├── acces.php                     ← Page d'accès refusé
│   ├── 404.php                       ← Page non trouvée
│   │
│   ├── admin/                        ← Vues réservées aux administrateurs
│   │   ├── dashboard.php             ← Tableau de bord avec statistiques
│   │   ├── admin-panel.php           ← Gestion des utilisateurs
│   │   ├── admin-restaurants.php     ← Gestion des restaurants
│   │   └── ajouter-admin.php         ← Ajout d'un compte admin
│   │
│   └── partials/                     ← Fragments réutilisés (layouts)
│       ├── head.php                  ← <head> public
│       ├── header.php                ← Navbar publique
│       ├── footer.php                ← Footer public
│       ├── foot.php                  ← Scripts JS de fin de page
│       ├── admin_head.php            ← <head> + sidebar admin (SB Admin)
│       ├── admin_foot.php            ← Scripts JS admin
│       └── panier.php                ← Widget panier
│
└── assets/                           ← Ressources statiques
    ├── css/                          ← Feuilles de style
    ├── js/                           ← Scripts JS publics
    ├── webfonts/                     ← Polices d'icônes (Font Awesome)
    ├── favicon.ico
    ├── admins/                       ← Assets du template d'administration
    └── img/                          ← Toutes les images du site
        ├── plats/                    ← Photos des plats uploadées
        ├── restaurants/              ← Photos des restaurants uploadées
        ├── banner/                   ← Bannières d'accueil
        ├── chefs/                    ← Photos d'équipe
        ├── common/                   ← Visuels communs
        ├── icon/                     ← Icônes décoratives
        ├── review/                   ← Visuels des avis
        └── tab-img/                  ← Visuels des onglets
```

Chaque dossier a un rôle clair et exclusif :

- **`classes/`** ne contient que du SQL et de la logique métier — aucun `echo`, aucun HTML.
- **`controllers/`** orchestre les modèles et préparent les données pour les vues — aucune requête SQL en direct, aucun HTML.
- **`views/`** ne contient que du HTML/affichage — aucune requête SQL.
- **`assets/`** ne contient que des ressources statiques (CSS, JS, images, polices).

Cette séparation stricte est ce qui rend le code maintenable : pour modifier un affichage je vais dans `views/`, pour changer une requête SQL je vais dans `classes/`, pour modifier la logique de traitement je vais dans `controllers/`. Aucune ambiguïté possible.

---

## 3. Le Front Controller : `index.php`

Toutes les requêtes HTTP arrivent sur `index.php`. Ce fichier joue le rôle de **point d'entrée unique** de l'application : c'est le pattern **Front Controller**.

À l'intérieur, j'ai défini une **table de routage** (`$dispatchMap`) qui associe chaque URL à un couple (contrôleur, méthode).

```php
$dispatchMap = [
    'accueil'                 => [new HomeController(),       'index'],
    'connexion'               => [new AuthController(),       'login'],
    'inscription'             => [new AuthController(),       'register'],
    'gestion-carte'           => [new PlatController(),       'gestionCarte'],
    'ajouter-plat'            => [new PlatController(),       'ajouter'],
    'modifier-plat'           => [new PlatController(),       'modifier'],
    'toggle-disponible-plat'  => [new PlatController(),       'toggleDisponible'],
    'update-plat-categorie'   => [new PlatController(),       'updateCategorie'],
    'details'                 => [new RestaurantController(), 'details'],
    'save-horaires'           => [new RestaurantController(), 'saveHoraires'],
    // ... une trentaine d'entrées au total
];
```

Quand un utilisateur arrive sur l'URL `gestion-carte`, le code suivant s'exécute :

```php
$page = isset($_GET['mod']) ? $_GET['mod'] : 'accueil';

if (isset($dispatchMap[$page])) {
    [$controller, $method] = $dispatchMap[$page];
    $viewData = $controller->$method();  // appelle PlatController::gestionCarte()
    if (is_array($viewData)) {
        extract($viewData);  // transforme les clés du tableau en variables PHP
    }
}
```

Le contrôleur retourne un tableau de données (par exemple `['resto' => ..., 'plats' => ...]`), et `extract()` les rend disponibles comme variables PHP (`$resto`, `$plats`) directement utilisables dans la vue.

---

## 4. Le flux complet d'une requête

Prenons un exemple concret : un restaurateur clique sur le lien **"Gestion de la carte"** de son restaurant.

```
1. Le navigateur envoie une requête : GET /gestion-carte?id=5
                          ↓
2. index.php reçoit la requête (Front Controller)
                          ↓
3. La table de routage associe 'gestion-carte' à PlatController::gestionCarte()
                          ↓
4. Le contrôleur vérifie les droits (test inline de $_SESSION : connecté + profil restaurateur)
                          ↓
5. Le contrôleur appelle les modèles (Restaurant::getOwnedBy, Plat::getByRestaurant)
                          ↓
6. Le contrôleur retourne un tableau ['resto' => ..., 'plats' => ...]
                          ↓
7. index.php fait extract() puis include('views/gestion-carte.php')
                          ↓
8. La vue génère le HTML qui est envoyé au navigateur
```

À chaque étape, **une seule** responsabilité est traitée. Le routage ne fait pas de SQL, le contrôleur ne fait pas de HTML, la vue ne fait pas de validation. C'est ça, MVC.

---

## 5. Le système de layouts

Une fois la vue principale chargée, `index.php` l'enveloppe automatiquement dans un **layout** (header + footer) selon le contexte :

```php
if ($is_admin_page) {
    // Pages d'administration : layout SB Admin
    include('views/partials/admin_head.php');
    include($page_url);
    include('views/partials/admin_foot.php');
} else {
    // Pages publiques : layout standard
    include('views/partials/head.php');
    include('views/partials/header.php');
    include($page_url);
    include('views/partials/footer.php');
}
```

Cela évite que chaque vue ait à réinclure le `<head>`, la navbar et le footer, ce qui serait du code dupliqué.

---

## 6. Bilan

L'architecture MVC + Front Controller apporte plusieurs avantages concrets :

- **Lisibilité** : pour trouver le code d'une fonctionnalité, je sais exactement où chercher (vue, contrôleur ou modèle selon le besoin).
- **Maintenabilité** : modifier l'affichage ne risque pas de casser la logique métier, et inversement.
- **Sécurité** : toutes les requêtes passent par `index.php`, ce qui me permet de centraliser des contrôles (authentification, layout, etc.). Aucun fichier de vue n'est accessible directement par URL.
- **Évolutivité** : ajouter une nouvelle page consiste à créer une vue, ajouter une méthode dans un contrôleur, et ajouter une entrée dans la table de routage. Le reste du projet n'est pas impacté.

Cette architecture est inspirée des frameworks PHP modernes (Symfony, Laravel) qui utilisent tous le même pattern, mais ici implémentée à la main pour bien comprendre les mécanismes internes.
