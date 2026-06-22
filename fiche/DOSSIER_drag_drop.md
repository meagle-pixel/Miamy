# Fonctionnalité front-end : Drag & Drop des plats avec SortableJS

## 1. Présentation de la fonctionnalité

Sur la page **Gestion de la carte** (`views/gestion-carte.php`), accessible au restaurateur connecté, tous les plats du restaurant sont regroupés par catégorie (Entrées, Plats, Desserts, Boissons, Snacks).

Pour permettre au restaurateur de **réorganiser sa carte à la souris**, j'ai implémenté un système de glisser-déposer : il peut prendre n'importe quel plat dans une catégorie et le faire glisser vers une autre. À chaque déplacement, la nouvelle catégorie est immédiatement enregistrée en base de données, **sans rechargement de la page**.

L'objectif est double : offrir une expérience utilisateur fluide et moderne, et éviter au restaurateur d'avoir à ouvrir un formulaire pour modifier la catégorie de chaque plat un par un.

> **Précision sur le périmètre de mon travail :** je me suis personnellement occupé du **drag & drop** (intégration de SortableJS, gestionnaire `onEnd`) et de l'**enregistrement en base de données** (appel AJAX et méthode contrôleur PHP côté serveur). Les fonctionnalités annexes d'amélioration UX présentes dans le fichier — mise à jour en temps réel des compteurs "X plats" affichés en haut de chaque catégorie, affichage automatique du placeholder "Aucun plat dans cette catégorie", et logique de `revert` visuel — ont été ajoutées par mon tuteur. Le présent dossier décrit donc uniquement les parties dont je suis l'auteur.

---

## 2. Choix technique : SortableJS

Coder un drag & drop entièrement à la main demande de gérer les événements souris (`mousedown`, `mousemove`, `mouseup`), le positionnement absolu de l'élément glissé, le calcul des zones de dépôt, l'animation, etc. C'est plusieurs centaines de lignes de code, et c'est un nid à bugs (notamment sur mobile/tactile).

J'ai donc choisi d'utiliser **SortableJS**, une librairie JavaScript open-source spécialisée. Elle est légère (~30 ko), elle gère nativement le tactile, et elle expose une API très simple.

```html
<!-- Import depuis un CDN public, pas d'installation locale nécessaire -->
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
```

SortableJS s'occupe uniquement de la **partie visuelle** du drag & drop (déplacer le DOM, animer, détecter le dépôt). La **persistance en base** reste de ma responsabilité : c'est moi qui envoie une requête AJAX au serveur après chaque déplacement.

---

## 3. Préparation du HTML

Pour que SortableJS et mon code JavaScript puissent travailler, j'ai préparé le HTML avec quelques classes et attributs `data-*` qui servent de points d'accroche.

```html
<!-- Une liste triable par catégorie -->
<div class="sortable-list" data-categorie="Entrées">

    <!-- Une carte de plat -->
    <div class="col-lg-12 mb-3" data-plat-id="12">

        <!-- Poignée de glisser-déposer -->
        <i class="fas fa-grip-vertical drag-handle text-muted"></i>

        <!-- ... contenu de la carte ... -->
    </div>

    <!-- ... autres plats ... -->
</div>
```

Quatre éléments sont essentiels :

- **`.sortable-list`** sur le conteneur de chaque catégorie : c'est ce que SortableJS va rendre triable.
- **`data-categorie="..."`** sur la liste : indique à quelle catégorie elle correspond. C'est cette valeur qu'on enverra au serveur lorsqu'un plat change de section.
- **`data-plat-id="..."`** sur chaque carte de plat : permet de retrouver l'identifiant du plat en base après un déplacement.
- **`.drag-handle`** sur l'icône "trois lignes verticales" : SortableJS est configuré pour n'autoriser le drag que via cette poignée. Cliquer ailleurs sur la carte (par exemple sur le bouton "Modifier") ne déclenche pas de drag.

Les attributs `data-*` sont du HTML5 natif. Ils permettent de stocker des informations métier (id en base, nom de catégorie) directement sur les éléments HTML, pour que le JavaScript puisse les lire avec `element.dataset.xxx`.

---

## 4. Initialisation de SortableJS

Au chargement de la page, je sélectionne toutes les listes `.sortable-list` (une par catégorie) et j'attache SortableJS à chacune.

```js
document.querySelectorAll('.sortable-list').forEach(function (el) {
    Sortable.create(el, {
        group:      'plats',              // même groupe = on peut glisser entre sections
        animation:  150,                  // animation fluide
        handle:     '.drag-handle',       // drag uniquement par la poignée
        ghostClass: 'drag-ghost',         // classe CSS sur la carte "fantôme"
        filter:     '.empty-placeholder', // le placeholder vide n'est pas draggable

        onEnd: async function (evt) { ... }
    });
});
```

Les options clés :

- **`group: 'plats'`** : c'est la magie de SortableJS. Toutes les listes qui partagent le même nom de groupe deviennent **interconnectées** — on peut donc glisser un plat de la liste "Entrées" vers la liste "Plats".
- **`handle: '.drag-handle'`** : restreint le drag à la poignée uniquement.
- **`ghostClass: 'drag-ghost'`** : SortableJS ajoute cette classe CSS sur l'élément pendant qu'il est glissé, ce qui me permet de le styliser (opacité réduite, bordure pointillée bleue).

Le callback `onEnd` est l'élément central de ma logique : il se déclenche à chaque fois que l'utilisateur lâche un plat.

---

## 5. Le gestionnaire `onEnd` — le cœur du système

C'est la fonction qui se déclenche **au moment où l'utilisateur lâche le plat**. Elle reçoit un objet `evt` (event) qui contient toutes les informations sur le déplacement.

```js
onEnd: async function (evt) {
    const platId   = evt.item.dataset.platId;     // id du plat déplacé
    const newCateg = evt.to.dataset.categorie;    // catégorie de destination
    const oldCateg = evt.from.dataset.categorie;  // catégorie d'origine

    // Si le plat est resté dans la même catégorie, rien à enregistrer
    if (newCateg === oldCateg) return;

    // --- Appel AJAX pour persister en base ---
    const fd = new FormData();
    fd.append('id_plat', platId);
    fd.append('categorie', newCateg);

    try {
        const response = await fetch(BASE_URL + '/update-plat-categorie', {
            method: 'POST',
            body: fd
        });
        const resp = await response.json();
        if (!resp.success) {
            alert('Erreur lors du changement de catégorie.');
        }
    } catch (err) {
        alert('Erreur réseau. Veuillez réessayer.');
    }
}
```

Cette fonction fait deux choses dans l'ordre :

**1. Extraction des informations.** Depuis l'objet `evt`, je récupère l'identifiant du plat (`platId`), sa catégorie d'origine (`oldCateg`) et sa catégorie de destination (`newCateg`). Si les deux catégories sont identiques (l'utilisateur a simplement réordonné un plat dans sa propre catégorie), je sors de la fonction immédiatement : il n'y a rien à enregistrer.

**2. Envoi de la requête AJAX.** J'utilise `fetch()` avec la syntaxe moderne `async/await` pour envoyer la nouvelle catégorie au serveur. Le serveur répond avec un objet JSON `{ success: true }` ou `{ success: false }`. Si la réponse indique un échec, ou si le réseau tombe, j'affiche une alerte à l'utilisateur.

---

## 6. La persistance côté serveur

Le point de terminaison appelé est `/update-plat-categorie`, qui est routé vers la méthode `PlatController::updateCategorie()`. Cette méthode :

```php
public function updateCategorie()
{
    header('Content-Type: application/json');

    // 1. L'utilisateur est-il un restaurateur connecté ?
    if (!isset($_SESSION['connected']) || $_SESSION['connected'] !== true || $_SESSION['user']['profil'] > 2) {
        echo json_encode(['success' => false, 'message' => 'Non autorise']);
        exit();
    }

    $id_plat         = isset($_POST['id_plat'])   ? (int)trim($_POST['id_plat']) : 0;
    $nouvelle_cat    = isset($_POST['categorie']) ? trim($_POST['categorie'])     : '';
    $id_restaurateur = (int)$_SESSION['user']['profil_id'];

    // 2. La catégorie envoyée fait-elle partie des catégories autorisées ?
    $categories_valides = ['Entrées', 'Plats', 'Desserts', 'Boissons', 'Snacks'];
    if (!$id_plat || !in_array($nouvelle_cat, $categories_valides, true)) {
        echo json_encode(['success' => false, 'message' => 'Donnees invalides']);
        exit();
    }

    $platClass = new Plat();

    // 3. Le plat appartient-il bien au restaurateur connecté ?
    if (!$platClass->isOwnedBy($id_plat, $id_restaurateur)) {
        echo json_encode(['success' => false, 'message' => 'Acces refuse']);
        exit();
    }

    // 4. Mise à jour effective en base
    $ok = $platClass->updateCategorie($id_plat, $nouvelle_cat);
    echo json_encode(['success' => (bool)$ok]);
    exit();
}
```

Trois vérifications de sécurité sont effectuées **systématiquement côté serveur** avant toute modification :

- L'utilisateur est-il connecté et possède-t-il le rôle restaurateur ?
- La catégorie envoyée fait-elle partie de la liste blanche `['Entrées', 'Plats', 'Desserts', 'Boissons', 'Snacks']` ? Cela empêche toute injection d'une valeur arbitraire.
- Le plat ciblé appartient-il bien au restaurateur connecté ? Cette vérification d'**ownership** empêche un restaurateur de modifier les plats d'un autre.

Ces contrôles sont essentiels : on ne fait jamais confiance aux données envoyées par le navigateur, car n'importe qui peut forger une requête HTTP avec un outil comme Postman.

La réponse renvoyée est un simple objet JSON `{ success: true }` ou `{ success: false, message: "..." }`, que le JavaScript sait interpréter.

---

## 7. Bilan et axes d'amélioration

Cette fonctionnalité combine **trois technologies** :

- **SortableJS** pour la partie visuelle du drag & drop (DOM, animation, gestion souris/tactile)
- **Fetch + async/await** pour la communication AJAX avec le serveur
- **PHP/MVC** pour la validation, la sécurité et la persistance en base

Le résultat est une interface fluide où le restaurateur peut réorganiser sa carte en quelques secondes, sans rechargement.

**Axes d'amélioration possibles :**

- Remplacer les `alert()` par des notifications visuelles plus modernes (toast Bootstrap).
- Ajouter un **token CSRF** sur la requête AJAX pour empêcher les attaques cross-site.
- Mémoriser également l'**ordre** des plats au sein d'une catégorie (actuellement seul le changement de catégorie est sauvegardé).
- Mutualiser le code AJAX dans une fonction utilitaire pour éviter la répétition entre le drag & drop et le toggle Dispo/Indispo.
