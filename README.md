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
       define('APP_URL', 'http://localhost/Miamy');
   } else {
       // PRODUCTION
       define('APP_URL', 'https://miamy.fr');
   }
   ```

2. ## BALISE <base> (views/partials/head.php)

   Ajouter après <head> :

   ```html
   <head>
     <base href="<?= APP_URL ?>/" />
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
   echo "<script>window.location.href='" . APP_URL . "/mon-compte-restaurateur';</script>";
   ```

   RÈGLE : Toujours utiliser APP_URL pour les redirections PHP.

## RÉSUMÉ DES 3 RÈGLES

┌─────────────────────────────────────────────────────────────────────────┐
│ 1. <base href="<?= APP_URL ?>/"> dans head.php │
│ 2. Liens HTML : jamais de "/" au début → href="connexion" │
│ 3. Redirections PHP : toujours APP_URL . "/page" │
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
Architecture MVC : chaque page passe par le routeur (index.php) qui
appelle une méthode de contrôleur, qui prépare les données puis inclut
la vue correspondante. Voir les sections "REFACTOR MVC" en bas du fichier
pour le détail de la mise en place.

PAGES PUBLIQUES
- Accueil               -> HomeController::index   (views/home.php)
- Connexion             -> AuthController::login   (views/login.php)
- Inscription resto     -> AuthController::register        (views/register.php)
- Inscription client    -> AuthController::registerClient  (views/register-client.php)
- Déconnexion           -> AuthController::logout  (header+exit, pas de vue)
- Liste des restaurants -> RestaurantController::liste     (views/liste-restaurants.php)

ESPACE RESTAURATEUR (profil = 2)
- Mon compte            -> UserController::monCompteRestaurateur
- Éditer profil         -> UserController::profilEditer
- Ajouter restaurant    -> RestaurantController::ajouter
- Modifier restaurant   -> RestaurantController::modifier
- Supprimer restaurant  -> RestaurantController::supprimer
- Gestion de la carte   -> PlatController::gestionCarte
- CRUD plats            -> PlatController::ajouter / modifier / supprimer
- Toggle dispo plat     -> PlatController::toggleDisponible   (AJAX, JSON)
- Changer catégorie     -> PlatController::updateCategorie    (AJAX, JSON)
- Sauver horaires       -> RestaurantController::saveHoraires

ESPACE ADMIN (profil = 1)
- Tableau de bord       -> AdminController::dashboard
- Gestion utilisateurs  -> AdminController::panel
- Gestion restaurants   -> AdminController::restaurants
- Ajouter un admin      -> AdminController::ajouterAdmin

CLASSES DE SERVICE (classes/)
- Database, User, UserLog, Restaurant, Restaurateur, Plat, Client,
  Category, Horaires, Page, ImageUploader, plus class.functions.php
  (utilitaires : get_ip, sanitizeString, array_sort).


TABLES BDD UTILISÉES
-----------------------------
- utilisateurs   : comptes de connexion (email, motdepasse, profil, profil_id)
- administrateurs: fiches admin (profil = 1)
- restaurateurs  : fiches gérant   (profil = 2)
- clients        : fiches client   (profil = 3)
- restaurants    : établissements
- plats          : carte d'un restau
- horaires       : 7 jours par restau
- categories     : types de cuisine (Français, Italien...)
- restaurant_categories : liaison restau ↔ catégorie
- pages          : routage URL → fichier PHP (utilisé par Page::getByMod)
- profils, autorisations : ACL en base (table support, peu utilisée)
- user_logs      : journal des actions utilisateur (UserLog::log)
- ips            : tracking IP (table support, code mort actuel)
- password_resets: tokens de réinit mot de passe


---

# DRAG & DROP — GESTION DE LA CARTE (gestion-carte.php)

## OBJECTIF

Permettre au restaurateur de déplacer un plat d'une catégorie à une autre
(ex : glisser un plat de "Plats" vers "Desserts") directement depuis
l'interface de gestion, sans passer par le formulaire de modification.

## LIBRAIRIE UTILISÉE : SortableJS

SortableJS est une librairie JavaScript open-source qui rend des éléments
HTML déplaçables par glisser-déposer. Elle est chargée via CDN :

```html
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
```

Aucune installation npm, aucune dépendance. L'appel AJAX utilise `fetch()`, l'API
native du navigateur (vanilla JavaScript, pas de jQuery requis).

## FICHIERS MODIFIÉS / CRÉÉS

1. views/gestion-carte.php  → modifié (HTML + JS)
2. PlatController::updateCategorie  → méthode AJAX (route /update-plat-categorie)

---

## 1. MODIFICATIONS HTML dans gestion-carte.php

### Boucle PHP sur toutes les catégories

AVANT : on bouclait sur $platsParCategorie, donc les catégories sans plat
n'apparaissaient pas du tout.

APRÈS : on boucle sur un tableau fixe de toutes les catégories connues,
pour qu'elles restent toujours visibles même si elles sont vides :

```php
$toutesCategories = ['Entrées', 'Plats', 'Desserts', 'Boissons', 'Snacks'];
foreach ($toutesCategories as $categorie):
    $platsCateg = $platsParCategorie[$categorie] ?? [];
```

### Attributs data-* ajoutés sur les éléments HTML

Les attributs data-* sont des attributs HTML5 personnalisés qui stockent
des données dans le DOM pour que le JavaScript puisse les lire.

```html
<!-- Sur chaque section catégorie -->
<div class="mb-5" data-categorie-section="Entrées">

<!-- Sur chaque liste de plats (= zone de dépôt SortableJS) -->
<div class="row sortable-list" data-categorie="Entrées">

<!-- Sur chaque carte de plat -->
<div class="col-lg-12 mb-3" data-plat-id="42">

<!-- Compteur mis à jour dynamiquement par le JS -->
<span data-count-number>3</span> plat<span data-count-plural>s</span>
```

### Poignée de drag (handle)

```html
<i class="fas fa-grip-vertical drag-handle"></i>
```

On utilise un handle plutôt que de rendre toute la carte draggable, pour
éviter les faux-clics sur les boutons Modifier / Supprimer.

### Placeholder pour catégories vides

```html
<div class="empty-placeholder">
    Aucun plat dans cette catégorie.
    Glissez-en un ici depuis une autre catégorie.
</div>
```

Affiché quand une catégorie est vide. Reste une zone de dépôt valide.
N'est pas lui-même draggable (géré côté JS avec filter).

---

## 2. JAVASCRIPT D'INITIALISATION

```javascript
Sortable.create(el, {
    group:   'plats',           // même nom → drag autorisé entre les listes
    handle:  '.drag-handle',    // seule la poignée déclenche le drag
    filter:  '.empty-placeholder', // le placeholder ne peut pas être dragué
    ghostClass: 'drag-ghost'    // classe CSS sur la carte "fantôme" pendant le drag
});
```

L'option GROUP est la clé : si toutes les listes partagent le même nom de
groupe, SortableJS autorise les éléments à passer de l'une à l'autre.
Sans ça, on ne peut réordonner qu'au sein d'une seule catégorie.

### Événement onEnd

Déclenché quand l'utilisateur lâche le plat. On y récupère :

```javascript
var platId   = evt.item.dataset.platId;      // ID du plat déplacé
var newCateg = evt.to.dataset.categorie;     // catégorie de destination
var oldCateg = evt.from.dataset.categorie;   // catégorie d'origine
```

Si les deux catégories sont identiques (simple réordonnancement), on ne fait rien.

### Mise à jour optimiste de l'interface

Avant même d'attendre la réponse du serveur, on met à jour l'interface
immédiatement (= mise à jour optimiste) :

- Le compteur de l'ancienne catégorie diminue de 1
- Le compteur de la nouvelle catégorie augmente de 1
- Si l'ancienne catégorie est vide, le placeholder s'affiche

Si l'AJAX échoue → la fonction revert() annule le déplacement visuel
et remet le plat à sa place d'origine.

---

## 3. ENDPOINT AJAX : PlatController::updateCategorie (route /update-plat-categorie)

Reçoit la requête JavaScript (méthode POST) et met à jour la base de données.

### Données reçues en POST

- id_plat    : l'ID du plat à modifier
- categorie  : la nouvelle catégorie

### Ce que fait le script (dans l'ordre)

1. SÉCURITÉ SESSION
   Vérifie que l'utilisateur est connecté et que c'est bien un restaurateur.

```php
if (!isset($_SESSION['connected']) || $_SESSION['user']['profil'] > 2) {
    echo json_encode(['success' => false, 'message' => 'Non autorisé']);
    exit();
}
```

2. VALIDATION DES DONNÉES
   La catégorie doit appartenir à une liste blanche.
   Cela empêche d'injecter n'importe quelle valeur en base de données.

```php
$categories_valides = ['Entrées', 'Plats', 'Desserts', 'Boissons', 'Snacks'];
if (!in_array($nouvelle_cat, $categories_valides, true)) { ... }
```

3. VÉRIFICATION DE PROPRIÉTÉ (sécurité essentielle)
   On vérifie que le plat appartient bien à un restaurant du restaurateur
   connecté. Sans cette vérification, n'importe quel restaurateur pourrait
   modifier les plats d'un autre.

```sql
SELECT p.id FROM plats p
JOIN restaurants r ON r.id = p.id_restaurant
WHERE p.id = ? AND r.id_restaurateur = ?
```

4. MISE À JOUR EN BASE

```sql
UPDATE plats SET categorie = ? WHERE id = ?
```

5. RÉPONSE JSON
   Le script retourne du JSON lu par le JavaScript :

```json
{"success": true}
{"success": false}
```

---

## SCHÉMA DU FLUX COMPLET

```
Utilisateur glisse un plat
        ↓
SortableJS détecte le drop (onEnd)
        ↓
JS met à jour l'interface immédiatement (optimiste)
        ↓
JS envoie fetch() → /update-plat-categorie → PlatController::updateCategorie
        ↓
PHP vérifie session + propriété + valide catégorie
        ↓
PHP fait UPDATE en base → retourne {"success": true}
        ↓
Si succès → rien à faire (interface déjà à jour)
Si erreur → JS annule le déplacement visuel (revert)
```

## POURQUOI C'EST INTÉRESSANT TECHNIQUEMENT

- **Pas de rechargement de page** : l'utilisateur a l'impression que
  la modif est instantanée.
- **Mise à jour optimiste** : l'UI bouge AVANT la réponse serveur, ce
  qui donne une sensation de fluidité.
- **Mécanisme de rollback** : si le serveur dit non, on annule
  visuellement le déplacement, donc l'utilisateur n'est jamais en
  désynchro avec la base.
- **Sécurité respectée** : 3 vérifications côté serveur (connecté,
  catégorie whitelisted, ownership du plat). Même si quelqu'un trichait
  avec son JS, le serveur refuserait.
- **Format JSON propre** : standard universel pour les API web.

## POUR LA PRÉSENTATION ORALE

Cette feature illustre 4 concepts importants à mentionner devant un jury :

- **L'AJAX** : appel HTTP asynchrone (fetch) qui modifie la base sans
  rechargement.
- **L'utilisation d'une librairie JS externe** : SortableJS via CDN, sans
  installation ni dépendance npm.
- **Le découplage frontend / backend** : le JS ne fait que parler à un
  endpoint HTTP qui renvoie du JSON — c'est exactement le même principe
  qu'une API REST moderne (React/Vue/Angular ↔ API PHP).
- **L'expérience utilisateur** : mise à jour optimiste + rollback en cas
  d'erreur = perception d'une interface instantanée et fiable.


---

# TOGGLE DISPONIBLE / INDISPONIBLE (gestion-carte.php)

## OBJECTIF

Permettre au restaurateur de basculer un plat entre "Disponible" et
"Indisponible" en un seul clic, directement depuis la liste des plats,
sans passer par le formulaire de modification.

## FICHIERS MODIFIÉS / CRÉÉS

1. views/gestion-carte.php  → modifié (HTML + JS)
2. PlatController::toggleDisponible  → méthode AJAX (route /toggle-disponible-plat)

---

## 1. MODIFICATIONS HTML dans gestion-carte.php

### Attribut data-badge-disponible sur le badge

On ajoute un attribut sur le badge vert/rouge affiché à côté du nom du plat,
pour que le JavaScript puisse le retrouver et le mettre à jour sans recharger
la page :

```html
<span class="badge bg-success ms-2" data-badge-disponible>Disponible</span>
```

### Bouton toggle dans les actions

Un bouton est ajouté à côté des boutons Modifier et Supprimer.
Son apparence change selon l'état du plat :

```html
<!-- Si le plat EST disponible → bouton orange pour le rendre indisponible -->
<button class="btn btn-outline-warning btn-toggle-dispo"
    data-plat-id="42"
    data-disponible="1">
    <i class="fas fa-eye-slash"></i> Indispo
</button>

<!-- Si le plat N'EST PAS disponible → bouton vert pour le rendre disponible -->
<button class="btn btn-outline-success btn-toggle-dispo"
    data-plat-id="42"
    data-disponible="0">
    <i class="fas fa-eye"></i> Dispo
</button>
```

data-plat-id : l'ID du plat, envoyé en AJAX au moment du clic
data-disponible : l'état actuel (1 ou 0), mis à jour par le JS après chaque toggle

---

## 2. JAVASCRIPT dans gestion-carte.php

On écoute les clics sur tous les boutons `.btn-toggle-dispo` via délégation
d'événement vanilla (un seul listener pour tous les boutons de la page).
Le bouton est désactivé pendant la requête pour éviter les double-clics.

```javascript
document.addEventListener('click', function (e) {
    const btn = e.target.closest('.btn-toggle-dispo');
    if (!btn) return;

    const platId = btn.dataset.platId;
    btn.disabled = true; // désactive pendant la requête

    const fd = new FormData();
    fd.append('id_plat', platId);

    fetch(BASE_URL + '/toggle-disponible-plat', {
        method: 'POST',
        body: fd
    })
    .then(function (response) { return response.json(); })
    .then(function (resp) {
        if (resp.success) {
            // resp.disponible = nouvel état retourné par le serveur (0 ou 1)
            // → mettre à jour le badge, le bouton, griser la carte
        }
    })
    .finally(function () { btn.disabled = false; });
});
```

### Mises à jour visuelles après le toggle (sans rechargement)

1. Le badge change de couleur et de texte :
   - bg-success "Disponible"  ←→  bg-danger "Indisponible"

2. Le bouton bascule :
   - btn-outline-warning + icône fa-eye-slash + texte "Indispo"
   ←→ btn-outline-success + icône fa-eye + texte "Dispo"

3. La carte est légèrement grisée (opacity-50) si le plat est indisponible,
   pour le distinguer visuellement dans la liste.

---

## 3. ENDPOINT AJAX : PlatController::toggleDisponible (route /toggle-disponible-plat)

Reçoit id_plat en POST et inverse la valeur de disponible en base.

### Ce que fait le script (dans l'ordre)

1. SÉCURITÉ SESSION — même principe que update-plat-categorie.php

2. VÉRIFICATION DE PROPRIÉTÉ
   Jointure SQL pour s'assurer que le plat appartient au restaurateur connecté.
   On récupère aussi la valeur actuelle de disponible pour calculer le nouvel état.

```sql
SELECT p.id, p.disponible FROM plats p
JOIN restaurants r ON r.id = p.id_restaurant
WHERE p.id = ? AND r.id_restaurateur = ?
```

3. TOGGLE EN BASE
   Appel à la méthode toggleDisponible() déjà présente dans class.plats.php :

```sql
UPDATE plats SET disponible = IF(disponible = 1, 0, 1) WHERE id = ?
```

   IF(disponible = 1, 0, 1) signifie : si c'est 1, mettre 0 ; sinon mettre 1.
   C'est un toggle atomique — pas besoin de lire puis réécrire en deux requêtes.

4. RÉPONSE JSON avec le nouvel état

```json
{"success": true,  "disponible": 0}
{"success": false, "disponible": 1}
```

   Le JS utilise disponible pour mettre à jour l'interface dans le bon sens,
   sans avoir à deviner ou recalculer côté client.

---

## SCHÉMA DU FLUX COMPLET

```
Utilisateur clique sur "Indispo" / "Dispo"
        ↓
JS désactive le bouton (anti double-clic)
        ↓
JS envoie fetch() → /toggle-disponible-plat → PlatController::toggleDisponible
        ↓
PHP vérifie session + propriété du plat
        ↓
PHP fait UPDATE (IF disponible = 1 → 0, sinon → 1)
        ↓
PHP retourne {"success": true, "disponible": 0}
        ↓
JS met à jour badge + bouton + grisage de la carte
JS réactive le bouton
```

---

## CORRECTION — Placeholder catégorie vide ne disparaissait pas

### Problème

Quand on glissait un plat vers une catégorie vide (qui affichait le placeholder
"Aucun plat dans cette catégorie"), le placeholder ne disparaissait pas
automatiquement. Il fallait recharger la page avec F5.

### Cause

Dans l'événement onEnd de SortableJS, on appelait checkEmpty uniquement sur
evt.from (la liste source qui se vide) mais jamais sur evt.to (la liste de
destination qui reçoit le plat).

```javascript
// AVANT — checkEmpty seulement sur la source
checkEmpty(evt.from);

// APRÈS — checkEmpty sur la source ET la destination
checkEmpty(evt.from); // cache le placeholder si la source se vide
checkEmpty(evt.to);   // cache le placeholder si la destination reçoit un plat
```

### Pourquoi ça fonctionne

La fonction checkEmpty vérifie si la liste contient des éléments avec
data-plat-id. Si oui → elle cache le placeholder. Si non → elle l'affiche.
En appelant checkEmpty sur evt.to après chaque drop, le placeholder de la
catégorie de destination est masqué dès que le plat y arrive.


---

# INTÉGRATION DU TEMPLATE SB ADMIN (PARTIE ADMINISTRATEUR)

## OBJECTIF

Utiliser le template SB Admin (Bootstrap 5, sidebar + topbar) uniquement
pour les pages d'administration, tout en gardant le template public pour
le reste du site (vitrine, espace client, espace restaurateur).

## PRINCIPE GÉNÉRAL

Le routeur `index.php` utilise deux squelettes HTML différents :

- Layout PUBLIC : head.php → header.php → page → footer.php → foot.php
- Layout ADMIN  : admin_head.php → page → admin_foot.php

Selon la page demandée, le routeur choisit lequel des deux charger.

---

## ÉTAPE 1 — COPIER LE TEMPLATE DANS LE PROJET

Le template SB Admin téléchargé contient des fichiers HTML d'exemple
(index.html, tables.html, charts.html, etc.). Ces fichiers ne sont PAS
copiés dans Miamy : ils servent uniquement de référence visuelle quand
on crée une nouvelle page admin (on y pioche des blocs HTML).

Seuls les fichiers CSS / JS sont copiés, dans `assets/admins/` :

```
Miamy/
└── assets/
    └── admins/
        ├── css/
        │   └── styles.css        (Bootstrap 5.2.3 + tous les styles SB Admin)
        ├── js/
        │   ├── scripts.js
        │   └── datatables-simple-demo.js
        └── assets/
            └── img/
```

Important : le dossier doit s'appeler `admins` (avec un s), parce que les
chemins dans les partials PHP référencent ce nom. Sinon le CSS ne charge
pas et la sidebar n'a aucun style.

---

## ÉTAPE 2 — CRÉER LE PARTIAL admin_head.php

Fichier : `views/partials/admin_head.php`

Ce partial ouvre le HTML, charge le CSS de SB Admin, dessine la topbar et
la sidebar, puis ouvre une balise `<main>` qui accueillera le contenu de
chaque page admin. Il NE FERME PAS `</main>`, `</body>` ni `</html>`
volontairement — c'est `admin_foot.php` qui s'en charge.

```php
<?php $current_mod = $_GET['mod'] ?? ''; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <base href="<?= $GLOBALS['url'] ?>/">
    <meta charset="utf-8" />
    <title><?= htmlspecialchars($page_title ?? 'Admin') ?> - Miamy</title>
    <link href="<?= $GLOBALS['url'] ?>/assets/admins/css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js"></script>
</head>
<body class="sb-nav-fixed">

    <!-- Topbar (logo + recherche + menu utilisateur) -->
    <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
        ...
    </nav>

    <div id="layoutSidenav">
        <!-- Sidebar (menu de navigation admin) -->
        <div id="layoutSidenav_nav">
            <nav class="sb-sidenav accordion sb-sidenav-dark">
                <a class="nav-link <?= $current_mod === 'dashboard' ? 'active' : '' ?>"
                   href="dashboard">
                    <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                    Tableau de bord
                </a>
                <a class="nav-link <?= $current_mod === 'admin-panel' ? 'active' : '' ?>"
                   href="admin-panel">
                    <div class="sb-nav-link-icon"><i class="fas fa-users"></i></div>
                    Utilisateurs
                </a>
            </nav>
        </div>

        <!-- Contenu (la page admin sera incluse ici) -->
        <div id="layoutSidenav_content">
            <main>
```

L'astuce `$current_mod === 'xxx' ? 'active' : ''` permet de surligner
automatiquement le lien de la page courante dans la sidebar.

---

## ÉTAPE 3 — CRÉER LE PARTIAL admin_foot.php

Fichier : `views/partials/admin_foot.php`

C'est le miroir de admin_head.php : il ferme tout ce que l'autre a ouvert
(`</main>`, `</body>`, `</html>`), affiche le footer, et charge les
scripts JavaScript.

```php
            </main>
            <footer class="py-4 bg-light mt-auto">
                <div class="container-fluid px-4">
                    <div class="text-muted">Copyright &copy; Miamy <?= date('Y') ?></div>
                </div>
            </footer>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= $GLOBALS['url'] ?>/assets/admins/js/scripts.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js"></script>
    <script src="<?= $GLOBALS['url'] ?>/assets/admins/js/datatables-simple-demo.js"></script>
</body>
</html>
```

---

## ÉTAPE 4 — ROUTEUR : DISPATCHMAP + DÉTECTION LAYOUT ADMIN

Le routeur d'origine incluait systématiquement les partials publics. Deux
choses ont été ajoutées :

(1) Une `$dispatchMap` qui mappe chaque slug d'URL vers la méthode du
contrôleur correspondante. C'est la table de routage centrale du projet.

(2) Une détection de layout admin basée sur le chemin de la vue : si le
fichier de vue est dans `views/admin/`, on inclut les partials admin
(template SB Admin) au lieu des partials publics.

```php
$dispatchMap = [
    'accueil'           => [new HomeController(),       'index'],
    'connexion'         => [new AuthController(),       'login'],
    // ... (toutes les routes du projet)
    'dashboard'         => [new AdminController(),      'dashboard'],
    'admin-panel'       => [new AdminController(),      'panel'],
    'admin-restaurants' => [new AdminController(),      'restaurants'],
    'ajouter-admin'     => [new AdminController(),      'ajouterAdmin'],
];

// Une page est admin si son fichier est dans views/admin/
$is_admin_page = strpos($page_url, 'views/admin/') === 0;

if ($is_admin_page) {
    include('views/partials/admin_head.php');
    include($page_url);
    include('views/partials/admin_foot.php');
} else {
    include('views/partials/head.php');
    include('views/partials/header.php');
    include($page_url);
    include('views/partials/footer.php');
    include('views/partials/foot.php');
}
```

Note : il n'y a plus de "liste blanche" séparée pour détecter le layout
admin (contrairement à une ancienne version). Le critère unique est le
chemin de la vue dans `views/admin/`. Tant qu'une nouvelle page admin
est créée dans ce dossier, elle bascule automatiquement sur le layout admin.

## ÉTAPE 5 — CRÉER UNE PAGE ADMIN

Les pages admin vivent dans `views/admin/`. Elles ne contiennent PAS de
balises `<html>`, `<head>` ou `<body>` — elles s'insèrent au milieu du
squelette préparé par les partials.

Une page admin doit donc :

1. Faire son traitement PHP (sécurité, requêtes BDD)
2. Encapsuler son HTML dans `<div class="container-fluid px-4">...</div>`
   (le conteneur standard SB Admin)

Squelette type :

```php
<?php
// Vérification d'accès admin (profil = 1)
if (!isset($_SESSION['connected']) || $_SESSION['user']['profil'] > 1) {
    header('Location: ' . $GLOBALS['url'] . '/connexion');
    exit();
}

$pdo = Database::getInstance()->getConnection();
// ... requêtes ...
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Mon titre</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="accueil">Accueil</a></li>
        <li class="breadcrumb-item active">Ma page</li>
    </ol>

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-table me-1"></i>
            Mon tableau
        </div>
        <div class="card-body">
            <table id="datatablesSimple">
                <thead><tr><th>...</th></tr></thead>
                <tbody>...</tbody>
            </table>
        </div>
    </div>
</div>
```

C'est là que les fichiers HTML du template original deviennent utiles :
ouvrir `tables.html` pour un tableau, `charts.html` pour un graphique,
etc., et copier le bloc qui se trouve à l'intérieur de `<main>...</main>`.

---

## ÉTAPE 6 — ENREGISTRER LA PAGE ET L'AJOUTER AU MENU

Trois actions à faire pour qu'une nouvelle page admin soit accessible :

### (a) Insérer la page dans la table `pages`

Via phpMyAdmin par exemple :

```sql
INSERT INTO pages (nom, `mod`, url)
VALUES ('Gestion des plats', 'gestion-plats', 'views/admin/gestion-plats.php');
```

### (b) Ajouter le slug et la méthode du contrôleur à `$dispatchMap` dans index.php

```php
$dispatchMap = [
    // ... routes existantes
    'gestion-plats' => [new AdminController(), 'gestionPlats'],
];
```

Et créer la méthode correspondante dans le contrôleur. Pour une page admin,
ajouter `AdminController::gestionPlats()` avec un bloc de vérification de session (profil ≤ 1)
en tête et retourne les données via `compact(...)`.

### (c) Ajouter un lien dans la sidebar de admin_head.php

```php
<a class="nav-link <?= $current_mod === 'gestion-plats' ? 'active' : '' ?>"
   href="gestion-plats">
    <div class="sb-nav-link-icon"><i class="fas fa-utensils"></i></div>
    Gestion des plats
</a>
```

La page est alors accessible à `/gestion-plats`, surlignée dans le menu,
encadrée par la sidebar et la topbar SB Admin.

---

## RÉCAP MÉMO — AJOUTER UNE PAGE ADMIN EN 4 ÉTAPES

1. Créer le fichier `views/admin/ma-page.php`
   → commence par `<div class="container-fluid px-4">`
2. Insérer la ligne dans la table `pages` (mod = slug, url = chemin)
3. Ajouter le slug + la méthode du contrôleur à `$dispatchMap` dans `index.php`
4. Ajouter le `<a class="nav-link">` correspondant dans `admin_head.php`

---

## PIÈGES À ÉVITER

- Le dossier dans assets/ doit s'appeler `admins` (pluriel), pas `admin`.
  Sinon le CSS retourne une 404 et la sidebar n'a aucun style.

- Une page admin ne doit JAMAIS contenir `<html>`, `<head>` ou `<body>` :
  ces balises sont déjà dans `admin_head.php` / `admin_foot.php`.

- Pas de `<section id="common_banner">` ni `<section class="section_padding">`
  sur les pages admin : ces éléments appartiennent au template public et
  cassent la mise en page SB Admin.

- Après chaque modif des partials, faire `Ctrl+F5` dans le navigateur
  pour casser le cache du CSS, sinon on continue à voir l'ancienne version.

---

## COULEURS DES BADGES DE LOG (dashboard.php)

La fonction `getActionBadgeStyle()` (en haut de `views/admin/dashboard.php`)
mappe chaque type d'action à une couleur de badge :

- login          → jaune
- logout         → rouge
- login_fail     → orange
- connect_as     → violet
- create_*       → vert
- update_*       → bleu
- reset_password → orange
- delete_*       → rouge foncé
- défaut         → gris

Pour ajouter un nouveau type d'action coloré, ajouter un `case` dans
le `switch` de la fonction. Sinon le badge sera gris par défaut.


# REFACTOR MVC — ÉTAPE 1 : ACCUEIL, CONNEXION, DÉCONNEXION

## CONTEXTE

Certaines vues contenaient encore de la logique métier (SQL, POST,
redirections) avant le HTML. Cette première passe nettoie 3 pages :

- `views/home.php`        → logique déplacée dans `HomeController::index()`
- `views/login.php`       → logique déplacée dans `AuthController::login()`
- `views/deconnexion.php` → déplacée dans `AuthController::logout()`,
  fichier SUPPRIMÉ.

`views/details.php` est volontairement exclu.

## FICHIERS CRÉÉS

- `controllers/HomeController.php` — `index()` : charge catégories et
  restaurants featured, retourne `compact('allCategories', 'featuredRestos', 'error_message')`.
- `controllers/AuthController.php` — `login()` (traite POST + redirige
  selon profil) et `logout()` (log + session_destroy + header).

## FICHIERS MODIFIÉS

- `index.php` : 3 nouvelles entrées dans `$dispatchMap` (`accueil`, `connexion`,
  `deconnexion`) ; le cas "pas de mod" devient `'accueil'` au lieu de bypasser
  le système.
- `views/home.php` : plus aucune logique en tête, juste un `?? []` de
  défense pour les variables attendues.
- `views/login.php` : le bloc PHP de traitement POST a disparu.

## FICHIER SUPPRIMÉ

`views/deconnexion.php` — La logique est dans `AuthController::logout()`
qui fait `header()+exit()` avant que `index.php` n'inclue la vue. Pour la
cohérence DB, on peut aussi exécuter :

```sql
UPDATE pages SET url = '' WHERE `mod` = 'deconnexion';
```


# REFACTOR MVC — ÉTAPE 2 : INSCRIPTIONS + ADMIN

## INSCRIPTIONS

Deux méthodes ajoutées à `AuthController` :

- `register()` — inscription restaurateur (profil 2). Valide les 6 champs,
  appelle `insertRestaurateur` puis `insertUtilisateur`. Renvoie 7 variables.
- `registerClient()` — inscription client (profil 3). 11 champs, appelle
  `insertClient` puis `insertUtilisateur`.

Vues `register.php` et `register-client.php` réduites au HTML pur. Routes
`inscription` et `inscription-client` ajoutées au `dispatchMap`.

## ADMIN

Nouveau `controllers/AdminController.php` avec :

- `requireAdmin()` (méthode privée d.origine, depuis simplifiée en bloc inline).
- `dashboard()` — 10 indicateurs (compteurs + dernières inscriptions /
  restaurants / logs).
- `panel()` — gestion utilisateurs (POST update + delete).
- `restaurants()` — gestion restaurants (POST delete + update_category).
- `ajouterAdmin()` — création d'un compte admin (profil 1).

La fonction `getActionBadgeStyle()` (mapping action → couleur de badge)
est restée dans `views/admin/dashboard.php` car purement présentationnelle.

Routes ajoutées : `dashboard`, `admin-panel`, `admin-restaurants`, `ajouter-admin`.


# REFACTOR MVC — ÉTAPE 3 : SUPPRESSION DE actions/

Le dossier `actions/` court-circuitait le routeur en exposant des fichiers
PHP directement par leur chemin (`/actions/save-horaires.php`). Tout passe
maintenant par `index.php`.

## MIGRÉ

- `actions/save-horaires.php`         → `RestaurantController::saveHoraires()`
- `actions/toggle-disponible-plat.php` → `PlatController::toggleDisponible()` (AJAX, JSON)
- `actions/update-plat-categorie.php`  → `PlatController::updateCategorie()`  (AJAX, JSON)

## URL MISES À JOUR DANS LES VUES

- `views/details.php`       : `action="...actions/save-horaires.php"` → `action="save-horaires"`
- `views/gestion-carte.php` : `fetch(BASE_URL + '/actions/update-plat-categorie.php')` → `'/update-plat-categorie'`
- `views/gestion-carte.php` : `fetch(BASE_URL + '/actions/toggle-disponible-plat.php')` → `'/toggle-disponible-plat'`

## SUPPRIMÉ

- `actions/changeRegion.php` (référence un `../login.php` qui n'existe plus)
- `actions/changepermission.php`

Le dossier `actions/` est entièrement supprimé.

## NOUVELLES ROUTES

```php
'toggle-disponible-plat' => [new PlatController(),       'toggleDisponible'],
'update-plat-categorie'  => [new PlatController(),       'updateCategorie'],
'save-horaires'          => [new RestaurantController(), 'saveHoraires'],
```


# REFACTOR MVC — ÉTAPE 4 : MODÈLES OO ET SQL HORS CONTRÔLEURS

## 4a — Sortir le SQL de PlatController et RestaurantController

Les deux contrôleurs faisaient encore du `$pdo->prepare(...)` directement,
la requête de vérification de propriété d'un restaurant était dupliquée 5 fois.

**Nouvelles méthodes de modèle** :
- `Restaurant` : `getOwnedBy`, `isOwnedBy`, `insert`, `addCategory`,
  `removeCategories`, `getCurrentCategoryId`, `updateInfoOwned`, `listByOwner`
- `Plat` : `getOwnedBy`, `isOwnedBy`, `updateCategorie`
- `Horaires` : `getTodayForRestaurants`

`PlatController` 449 → 400 lignes, `RestaurantController` 296 → 254 lignes.
Plus aucun SQL brut dans ces deux contrôleurs.

## 4b — Homogénéiser classes/ (procédural → OO)

Le dossier `classes/` mélangeait de vraies classes et des fichiers
`class.X.php` qui ne contenaient que des fonctions globales. Tout est
maintenant OO.

- `class.restaurateurs.php` → classe `Restaurateur` (4 méthodes).
  `getRestaurantsByOwner` est devenu `Restaurant::listByOwner`.
- `class.clients.php` → classe `Client` (10 méthodes pour le futur panier).
  Seul `insert()` est activement utilisé aujourd'hui.
- `class.pages.php` → classe `Page` (9 méthodes). Seul `getByMod()`
  (anciennement `getPage`) est active — appelée par le routeur.
- `class.users.php` (690 lignes, 27 fonctions dont 19 mortes) → classe
  `User` (6 méthodes actives, 294 lignes) + classe `UserLog` (1 méthode).
  Les 19 fonctions mortes ont été virées (historique dans git).

Au passage, `isClear($profil, $page)` était la même chose que
`Page::hasAccess($page, $profil)` (params inversés) : un duplicat de moins.


# REFACTOR MVC — ÉTAPE 6 : HELPER ImageUploader

L'upload d'images était dupliqué dans 4 méthodes (PlatController::ajouter,
modifier + RestaurantController::ajouter, modifier). Tout passe maintenant
par une seule classe.

## NOUVELLE CLASSE ImageUploader (classes/class.imageuploader.php)

Centralise la validation (extensions JPG/JPEG/PNG/WebP, taille max 5 Mo),
la création du dossier cible si nécessaire et l'appel à `move_uploaded_file`.

## USAGE

```php
$uploader   = new ImageUploader('plats');   // ou 'restaurants'
$image_name = $uploader->upload($_FILES['image'], $basename);

if ($image_name) {
    // OK, $image_name = "entrecote-1748000000.jpg" par exemple
} elseif ($uploader->error) {
    // Erreur de validation/upload : $uploader->error contient le message
}
```

## AVANT / APRÈS

Avant (répété 4 fois, ~20 lignes chaque fois) :

```php
if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
    $allowed = ['jpg', 'jpeg', 'png', 'webp'];
    $ext     = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
    if (in_array($ext, $allowed) && $_FILES['image']['size'] < 5000000) {
        $slug_plat   = strtolower(preg_replace('/[^A-Za-z0-9-]+/', '-', $nom));
        $image_name  = $slug_plat . '-' . time() . '.' . $ext;
        $upload_dir  = $GLOBALS['dev']
            ? $_SERVER['DOCUMENT_ROOT'] . '/Miamy/assets/img/plats/'
            : $_SERVER['DOCUMENT_ROOT'] . '/assets/img/plats/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
        if (!move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $image_name)) {
            $message_error = "Erreur lors de l'upload de l'image.";
        }
    } else {
        $message_error = "Image invalide (formats acceptés : JPG, PNG, WebP — max 5 Mo).";
    }
}
```

Après (5 lignes) :

```php
$uploader   = new ImageUploader('plats');
$slug_plat  = strtolower(preg_replace('/[^A-Za-z0-9-]+/', '-', $nom));
$image_name = $uploader->upload($_FILES['image'], $slug_plat . '-' . time());
if ($uploader->error) {
    $message_error = $uploader->error;
}
```

## POUR ÉTENDRE PLUS TARD

Si tu veux accepter le format GIF, modifier la taille max, ajouter des
miniatures automatiques, valider le contenu réel du fichier, ou passer
en CDN (S3, Cloudinary…), tu modifies UN seul fichier
(`class.imageuploader.php`) et les 4 endroits en profitent automatiquement.


# BILAN GLOBAL DU REFACTOR MVC

Les 6 points du rapport initial ont été abordés :

| # | Sujet                                          | Statut |
|---|------------------------------------------------|--------|
| 1 | Vues = contrôleurs déguisés                    | Étapes 1 + 2 |
| 2 | Dossier actions/                               | Étape 3 |
| 3 | SQL brut dans PlatController/RestaurantController | Étape 4 |
| 4 | classes/ hétérogène (procédural + OO)          | Étape 4 bis |
| 5 | Blocs de permission copiés-collés              | Bloc `if` inline directement dans chaque méthode |
| 6 | Upload d'images dupliqué                       | Étape 6 |

`views/details.php` est volontairement resté hors scope (décision initiale).

# LE DOSSIER assets/webfonts/ — POLICES D'ICÔNES

## Pourquoi il existe

Le dossier `assets/webfonts/` (3,2 Mo, 28 fichiers) contient les fichiers
de polices nécessaires à **Font Awesome** (les `<i class="fas fa-X">`,
`fas fa-user`, `fas fa-store`, etc. qu'on retrouve partout dans les vues)
et à **Flaticon** (un autre pack d'icônes du template original).

Il est arrivé dans le projet avec le template HTML initial qu'on a
téléchargé pour démarrer Miamy — il faisait partie du ZIP de base.

## Comment ça marche techniquement

Chaque icône qu'on utilise dans le HTML, par exemple :

```html
<i class="fas fa-user"></i>
```

n'est PAS une image. C'est un caractère spécial dans une police (comme
la lettre A est un caractère dans Arial). Le CSS de Font Awesome dit
au navigateur :
- "ce <i> doit afficher le caractère unicode 0xF007"
- "avec la police Font Awesome Solid"

Le navigateur va alors chercher la police dans `assets/webfonts/` et
dessine l'icône correspondante.

Conséquence : on peut utiliser autant d'icônes qu'on veut dans le code,
le dossier `webfonts/` garde toujours la même taille. Il contient toutes
les icônes existantes (~2000) en une seule fois.

## Deux mécanismes différents dans le projet

Le site public et la partie admin chargent Font Awesome différemment :

**Site public** (`views/partials/head.php` ligne 15) — chargement LOCAL :

```html
<link rel="stylesheet" href="<?= $GLOBALS['url'] ?>/assets/css/fontawesome.all.min.css" />
```

Le CSS local contient des règles `@font-face` qui pointent vers
`assets/webfonts/fa-solid-900.woff2` (etc.). Le site fonctionne sans
internet.

**Partie admin** (`views/partials/admin_head.php` ligne 20) — chargement CDN :

```html
<script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
```

Charge depuis le serveur officiel Font Awesome. Nécessite une connexion
internet. Hérité du template SB Admin tel quel.

À unifier un jour (tout en local OU tout en CDN) pour la cohérence,
mais ce n'est pas critique.

## NE PAS SUPPRIMER

Si le dossier `assets/webfonts/` est supprimé, toutes les icônes du
site public deviennent des rectangles vides. C'est un dossier essentiel.

# MIGRATION $GLOBALS → define() — CONSTANTES DE CONFIGURATION

## POURQUOI

Avant : la configuration (URL de base, mode dev, credentials DB, sel de
mot de passe) était stockée dans des variables `$GLOBALS['xxx']` —
**121 occurrences** dans le code. Problèmes : variables mutables (n'importe
quel bout de code pouvait les réécrire), pas de typage, syntaxe verbeuse.

Après : ces 7 valeurs sont des constantes PHP via `define()`. Immuables,
plus rapides, plus lisibles, c'est la convention standard PHP (utilisée
par WordPress, Drupal, etc.).

## LES 7 CONSTANTES

Définies dans `config.php` :

```php
define('DB_HOST',     $_ENV['DEV_DB_HOST'] ?? 'mysql-server');
define('DB_USERNAME', $_ENV['DEV_DB_USER'] ?? 'root');
define('DB_PASSWORD', $_ENV['DEV_DB_PASS'] ?? 'root');
define('DB_NAME',     $_ENV['DEV_DB_NAME'] ?? 'Miamy');
define('APP_URL',     $_ENV['DEV_URL']     ?? 'http://localhost/Miamy');
define('APP_DEV',     true);
```

(Les valeurs DB et URL changent en production vs local.)

## AVANT / APRÈS

```php
// AVANT
header('Location: ' . $GLOBALS['url'] . '/connexion');
if ($GLOBALS['dev']) { ... }

// APRÈS
header('Location: ' . APP_URL . '/connexion');
if (APP_DEV) { ... }
```

3 différences syntaxiques : pas de `$`, pas de quotes, pas de notation
tableau `[...]`. Une constante est juste un identifiant.

## OÙ ÇA APPARAÎT

- Tous les `header('Location: ...')` des contrôleurs
- Les `<a href="<?= APP_URL ?>/...">` et `<img src="...">` dans les vues
- Les `<form action="<?= APP_URL ?>/...">` dans les formulaires
- `config.php` (pour définir les constantes)
- `classes/class.database.php` (charge `DB_HOST`, `DB_USERNAME`, etc. pour PDO)
- `classes/class.users.php` (hache les mots de passe avec bcrypt via `password_hash`)

## MIGRATION EFFECTUÉE

- 121 occurrences `$GLOBALS['X']` remplacées par leur constante équivalente
- 65 fichiers PHP touchés
- Parser PHP : OK sur les 66 fichiers du projet après migration
- 0 référence résiduelle à `$GLOBALS['url|dev|base_salt|db_host|db_username|db_password|db_name']`
