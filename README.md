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

Aucune installation npm, aucune dépendance. jQuery (déjà présent) est utilisé
uniquement pour l'appel AJAX.

## FICHIERS MODIFIÉS / CRÉÉS

1. views/gestion-carte.php  → modifié (HTML + JS)
2. actions/update-plat-categorie.php  → créé (endpoint AJAX)

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

## 3. FICHIER AJAX : actions/update-plat-categorie.php

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
JS envoie $.post() → actions/update-plat-categorie.php
        ↓
PHP vérifie session + propriété + valide catégorie
        ↓
PHP fait UPDATE en base → retourne {"success": true}
        ↓
Si succès → rien à faire (interface déjà à jour)
Si erreur → JS annule le déplacement visuel (revert)
```


---

# TOGGLE DISPONIBLE / INDISPONIBLE (gestion-carte.php)

## OBJECTIF

Permettre au restaurateur de basculer un plat entre "Disponible" et
"Indisponible" en un seul clic, directement depuis la liste des plats,
sans passer par le formulaire de modification.

## FICHIERS MODIFIÉS / CRÉÉS

1. views/gestion-carte.php  → modifié (HTML + JS)
2. actions/toggle-disponible-plat.php  → créé (endpoint AJAX)

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

On écoute les clics sur tous les boutons .btn-toggle-dispo avec jQuery.
Le bouton est désactivé pendant la requête pour éviter les double-clics.

```javascript
$(document).on('click', '.btn-toggle-dispo', function () {
    var btn    = $(this);
    var platId = btn.data('plat-id');

    btn.prop('disabled', true); // désactive pendant la requête

    $.post(BASE_URL + '/actions/toggle-disponible-plat.php', { id_plat: platId },
    function (resp) {
        if (resp.success) {
            // resp.disponible = nouvel état retourné par le serveur (0 ou 1)
            // → mettre à jour le badge, le bouton, griser la carte
        }
    }, 'json')
    .always(function () { btn.prop('disabled', false); });
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

## 3. FICHIER AJAX : actions/toggle-disponible-plat.php

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
JS envoie $.post() → actions/toggle-disponible-plat.php
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


