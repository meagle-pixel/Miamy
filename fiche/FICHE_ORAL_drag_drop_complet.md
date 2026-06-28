# Fiche oral — Drag & Drop des plats (front-end + back-end)

> Le processus complet, dans l'ordre : d'abord le front-end (HTML, SortableJS, AJAX), puis le back-end (routage, sécurité, base).
> Tout a été vérifié dans le code réel (`views/gestion-carte.php`, `controllers/PlatController.php`, `classes/class.plats.php`).

## Vue d'ensemble

Le restaurateur fait glisser un plat d'une catégorie à une autre. Le changement est enregistré en base **sans recharger la page**, grâce à l'AJAX.

```
HTML préparé → SortableJS (glissement) → onEnd → fetch AJAX
   → route update-plat-categorie → contrôleur (3 contrôles) → modèle (UPDATE) → réponse JSON → JS
```

---
---

# PARTIE 1 — LE FRONT-END

## 1. Préparer le HTML avec des points d'accroche
**Fichier : `views/gestion-carte.php`**

Avant tout, j'annote mon HTML avec des repères que le JavaScript pourra attraper :

```html
<!-- conteneur d'une catégorie : triable + nom de la catégorie -->
<div class="row sortable-list" data-categorie="<?= htmlspecialchars($categorie) ?>">

    <!-- une carte de plat : porte l'id du plat -->
    <div class="col-lg-12 mb-3" data-plat-id="<?= $plat['id'] ?>">

        <!-- la poignée par laquelle on attrape le plat -->
        <i class="fas fa-grip-vertical drag-handle"></i>

        <!-- ... image, nom, prix, boutons ... -->
    </div>
</div>
```

Les quatre points d'accroche :
- **`.sortable-list`** : marque le conteneur d'une catégorie comme triable.
- **`data-categorie`** : indique de quelle catégorie il s'agit.
- **`data-plat-id`** : retient l'identifiant du plat (pour le retrouver en base).
- **`.drag-handle`** : la poignée ; on ne peut attraper le plat que par là.

Tout ce HTML est généré par PHP avec une boucle `foreach` sur les plats.

## 2. Importer SortableJS et l'initialiser
**Fichier : `views/gestion-carte.php`**

```html
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
```

```js
// j'active SortableJS sur chaque liste de catégorie
document.querySelectorAll('.sortable-list').forEach(function (el) {
    Sortable.create(el, {
        group:  'plats',         // même groupe = on peut glisser d'une catégorie à l'autre
        handle: '.drag-handle',  // on n'attrape le plat que par la poignée
        onEnd:  async function (evt) { /* voir étape 3 */ }
    });
});
```

Points clés :
- `Sortable.create(el, {...})` est la méthode de SortableJS qui active le glisser-déposer sur un élément. (`new Sortable(el, {...})` est équivalent.)
- Je n'utilise **pas** `addEventListener` : c'est SortableJS qui gère le glissement, je lui passe juste des fonctions de rappel (callbacks) dans ses options.
- `document.querySelectorAll('.sortable-list')` récupère toutes mes listes de catégories, et `forEach` les parcourt une par une ; à chaque tour, `el` est la liste courante.

## 3. La fonction onEnd : récupérer les infos et les envoyer
**Fichier : `views/gestion-carte.php`**

`onEnd` se déclenche au moment où l'utilisateur **lâche** un plat.

```js
onEnd: async function (evt) {

    // 1. Je récupère l'id du plat et sa catégorie d'arrivée
    const platId   = evt.item.dataset.platId;
    const newCateg = evt.to.dataset.categorie;
    const oldCateg = evt.from.dataset.categorie;

    // 2. Si c'est la même catégorie, rien à enregistrer
    if (newCateg === oldCateg) return;

    // 3. Je prépare les données à envoyer
    const fd = new FormData();
    fd.append('id_plat', platId);
    fd.append('categorie', newCateg);

    // 4. Je les envoie au serveur en AJAX, sans recharger la page
    const response = await fetch(BASE_URL + '/update-plat-categorie', {
        method: 'POST',
        body: fd
    });

    // 5. Je lis la réponse (JSON) pour savoir si ça a marché
    const resp = await response.json();
    // (si resp.success est faux, je remets le plat à sa place)
}
```

À retenir :
- `evt` est fourni par SortableJS ; dedans, `evt.item` est le plat déplacé, `evt.to` la liste d'arrivée, `evt.from` la liste de départ.
- `FormData` est un objet natif du navigateur : un « paquet » de données. `append(nom, valeur)` y ajoute une donnée.
- `fetch(...)` envoie ce paquet en **POST** vers la route `update-plat-categorie`. C'est ça, l'AJAX : on parle au serveur en arrière-plan.

---
---

# PARTIE 2 — LE BACK-END

## 4. Le routage amène la requête au bon endroit
**Fichier : `index.php`**

```php
'update-plat-categorie' => [new PlatController(), 'updateCategorie'],
```

La route `/update-plat-categorie` passe par le routeur habituel (`.htaccess` → `index.php` → `$dispatchMap`) et appelle la méthode **`PlatController::updateCategorie()`**. La différence avec une page normale : elle renvoie du **JSON**, pas du HTML.

## 5. Le contrôleur : 3 contrôles de sécurité avant d'écrire
**Fichier : `controllers/PlatController.php`**

Principe fondamental : **on ne fait jamais confiance à ce que le navigateur envoie.**

```php
public function updateCategorie()
{
    header('Content-Type: application/json');   // je réponds en JSON

    // CONTRÔLE 1 : connecté + bon rôle (restaurateur ou admin)
    if (!isset($_SESSION['connected']) || $_SESSION['connected'] !== true || $_SESSION['user']['profil'] > 2) {
        echo json_encode(['success' => false, 'message' => 'Non autorise']); exit();
    }

    $id_plat         = isset($_POST['id_plat'])   ? (int)trim($_POST['id_plat']) : 0;
    $nouvelle_cat    = isset($_POST['categorie']) ? trim($_POST['categorie'])    : '';
    $id_restaurateur = (int)$_SESSION['user']['profil_id'];

    $categories_valides = ['Entrées', 'Plats', 'Desserts', 'Boissons', 'Snacks'];

    // CONTRÔLE 2 : données valides + catégorie dans la liste blanche
    if (!$id_plat || !in_array($nouvelle_cat, $categories_valides, true)) {
        echo json_encode(['success' => false, 'message' => 'Donnees invalides']); exit();
    }

    $platClass = new Plat();

    // CONTRÔLE 3 : le plat appartient-il bien à ce restaurateur ?
    if (!$platClass->isOwnedBy($id_plat, $id_restaurateur)) {
        echo json_encode(['success' => false, 'message' => 'acces refuse']); exit();
    }

    // Tout est bon → on enregistre
    $ok = $platClass->updateCategorie($id_plat, $nouvelle_cat);
    echo json_encode(['success' => (bool)$ok]);
    exit();
}
```

Les trois contrôles :
1. **Authentification + rôle** : l'utilisateur est connecté et a le profil ≤ 2 (restaurateur ou admin).
2. **Validation + liste blanche** : la catégorie reçue doit obligatoirement faire partie de ma liste autorisée. Ça empêche d'injecter une catégorie arbitraire. (`in_array(..., true)` = comparaison stricte.)
3. **Ownership** : le plat appartient bien au restaurateur connecté (voir étape 6).

## 6. Le contrôle d'ownership (jointure SQL)
**Fichier : `classes/class.plats.php`**

```php
public function isOwnedBy(int $idPlat, int $idRestaurateur): bool
{
    $stmt = $this->pdo->prepare(
        "SELECT p.id
         FROM `plats` p
         JOIN `restaurants` r ON r.id_restaurant = p.id_restaurant
         WHERE p.id = :id_plat AND r.id_restaurateur = :id_restaurateur"
    );
    $stmt->execute([
        'id_plat'         => $idPlat,
        'id_restaurateur' => $idRestaurateur,
    ]);
    return (bool)$stmt->fetch();
}
```

Cette requête fait une **jointure** entre `plats` et `restaurants`. Elle cherche un plat qui a cet id **et** dont le restaurant appartient au restaurateur connecté. Si elle trouve une ligne → le plat lui appartient (`true`). Sinon → accès refusé (`false`). C'est ce qui empêche un restaurateur de modifier le plat d'un autre en trafiquant l'id.

## 7. L'écriture en base (le modèle)
**Fichier : `classes/class.plats.php`**

```php
public function updateCategorie(int $idPlat, string $categorie): bool
{
    $stmt = $this->pdo->prepare("UPDATE `plats` SET `categorie` = :categorie WHERE `id` = :id");
    return $stmt->execute(['categorie' => $categorie, 'id' => $idPlat]);
}
```

Une simple **requête préparée** `UPDATE` qui change la catégorie du plat, protégée contre les injections SQL.

## 8. La réponse repart vers le JavaScript

Le contrôleur renvoie `{success: true}` (ou `false`) en JSON. Le `onEnd` côté JS lit cette réponse : si c'est un succès, l'affichage est déjà à jour ; sinon, il remet le plat à sa place.

---
---

# ❓ Questions / Réponses jury

**Q — Pourquoi pas de `addEventListener` pour le drag & drop ?**
R — Parce que c'est SortableJS qui gère les événements de glissement. Je lui passe juste des fonctions de rappel dans ses options, comme `onEnd`, qu'il déclenche au bon moment.

**Q — Comment le serveur sait quel plat déplacer ?**
R — Le JavaScript envoie l'id du plat et la nouvelle catégorie en POST. Le contrôleur les récupère dans `$_POST`.

**Q — Comment empêches-tu un restaurateur de modifier le plat d'un autre ?**
R — Avec le contrôle d'ownership : une jointure SQL qui vérifie que le plat appartient à un restaurant du restaurateur connecté. Sinon, accès refusé.

**Q — Et si quelqu'un envoie une fausse catégorie ?**
R — Je la rejette grâce à une liste blanche : la catégorie doit faire partie de mes valeurs autorisées, sinon je m'arrête.

**Q — Pourquoi répondre en JSON et pas en HTML ?**
R — Parce que c'est mon JavaScript qui lit la réponse, pas un humain. Le JSON est un format simple que le JS sait interpréter directement.

# Phrase de synthèse

> Le restaurateur glisse un plat ; SortableJS gère le glissement et m'appelle quand il le lâche. Je récupère l'id et la nouvelle catégorie, et je les envoie au serveur en AJAX. Côté serveur, je fais trois contrôles avant d'écrire : le rôle, une liste blanche des catégories, et surtout l'ownership du plat. Si tout est bon, j'enregistre avec une requête préparée et je réponds en JSON, que le JavaScript relit.
