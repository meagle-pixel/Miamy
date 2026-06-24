# Le routage de Miamy — de A à Z (tout comprendre)

> Objectif : savoir expliquer, étape par étape, comment une URL tapée par un visiteur
> arrive jusqu'à la bonne page affichée. Tout est vérifié sur le code réel.

---

## L'image à garder en tête

`index.php` = **l'accueil unique d'un immeuble**. Peu importe la page demandée, tout le monde
passe par cet accueil, qui consulte un **annuaire** (`$dispatchMap`) pour envoyer chaque visiteur
au bon bureau (le contrôleur) et à la bonne personne (la méthode).

**Le trajet complet :**

```
URL tapée → .htaccess → index.php → $dispatchMap → Contrôleur → (Modèle/BDD)
          → données → table `pages` → Vue → Layout → page affichée
```

On suit un exemple unique tout du long : le visiteur tape **`/connexion`**.

---

## Étape 0 — Le pattern Front Controller

Toutes les requêtes du site passent par **un seul fichier d'entrée**, `index.php`.
C'est le pattern **Front Controller**. Au lieu d'avoir un fichier PHP par page,
j'ai un point d'entrée unique qui centralise ce qui est commun (session, config,
chargement des classes, choix du layout) — écrit une seule fois.

---

## Étape 1 — `.htaccess` réécrit l'URL

Le navigateur demande `/connexion`. Le fichier `.htaccess` à la racine intercepte et réécrit :

```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f      # si ce n'est pas un vrai fichier
RewriteCond %{REQUEST_FILENAME} !-d      # ni un vrai dossier
RewriteRule ^([a-zA-Z0-9\-_]+)/?$ index.php?mod=$1 [L,QSA]
```

**En français :** « si l'adresse demandée ne correspond pas à un fichier (`!-f`) ni à un dossier
(`!-d`) réel, alors envoie tout vers `index.php` en plaçant le nom de la page dans un paramètre `mod`. »

- `/connexion` devient en interne `index.php?mod=connexion`.
- Les deux `RewriteCond` servent à **laisser passer les vrais fichiers** (images, CSS, JS) sans les router.
- `[L]` = stop (dernière règle), `[QSA]` = conserve les paramètres déjà présents (ex. `?id=5`).
- **L'utilisateur ne voit pas cette réécriture** : son URL reste propre dans la barre d'adresse.
  C'est plus lisible et meilleur pour le référencement (SEO).

---

## Étape 2 — `index.php` démarre tout

```php
ob_start();
include('functions.php');

require_once('controllers/AdminController.php');
require_once('controllers/AuthController.php');
require_once('controllers/HomeController.php');
require_once('controllers/PlatController.php');
require_once('controllers/RestaurantController.php');
require_once('controllers/UserController.php');
```

- `ob_start()` : met la sortie HTML **en tampon**, ce qui permet d'utiliser des redirections
  `header()` à tout moment sans l'erreur « headers already sent ».
- `include('functions.php')` : le fichier d'amorçage (voir étape 3).
- `require_once(...)` : **charge** les fichiers de contrôleurs (rend les classes disponibles).
  À ce stade, **aucun objet n'est créé** — on a juste « posé les livres de recettes sur le plan de travail ».

---

## Étape 3 — `functions.php` prépare le terrain

```php
session_start();                            // 1. active $_SESSION (mémoire de session)
date_default_timezone_set('Europe/Paris');  // 2. fuseau horaire français
require_once('config.php');                 // 3. configuration (BDD, APP_URL)
require_once('classes/class.database.php'); // 4. charge toutes les classes
require_once('classes/class.users.php');
// … etc.
```

C'est lui qui démarre la session et charge la config + toutes les classes. Il est inclus
tout en haut d'`index.php`, donc exécuté **à chaque requête**.

---

## Étape 4 — Récupérer la page demandée (`$page`)

```php
$page = isset($_GET['mod']) ? $_GET['mod'] : 'accueil';
```

- `$_GET['mod']` = la valeur du paramètre `mod`, c'est-à-dire le nom de la page,
  rempli par le `.htaccess`.
- C'est un **ternaire** (un if/else raccourci) : s'il y a un `mod`, `$page` prend sa valeur ;
  sinon, `$page` vaut `'accueil'` par défaut.
- Pour `/connexion`, on a donc `$page = 'connexion'`.

---

## Étape 5 — L'annuaire de routage (`$dispatchMap`)

```php
$dispatchMap = [
    'accueil'       => [new HomeController(),       'index'],
    'connexion'     => [new AuthController(),       'login'],
    'gestion-carte' => [new PlatController(),       'gestionCarte'],
    // … une ligne par page
];
```

`$dispatchMap` est un **tableau associatif** : une liste à étiquettes.
- **la clé** = le nom de la page (`'connexion'`)
- **la valeur** = une **paire** : `[new AuthController(), 'login']`, c'est-à-dire
  **quel contrôleur** utiliser et **quelle méthode** (action) appeler dedans.

Image : c'est le **tableau d'accueil de l'immeuble**. Je demande « connexion », il répond
« bureau AuthController, demande la fonction login ».

⚠️ Attention au vocabulaire : `'login'`, `'index'`… sont des **méthodes** de contrôleurs,
**pas** des modèles. Les modèles (User, Plat…) sont appelés *à l'intérieur* de ces méthodes.

---

## Étape 6 — On utilise l'annuaire (le cœur du routeur)

```php
if (isset($dispatchMap[$page])) {
    [$controller, $method] = $dispatchMap[$page];   // déstructuration
    $viewData = $controller->$method();             // appel dynamique
    if (is_array($viewData)) {
        extract($viewData);                          // tableau → variables
    }
}
```

Ligne par ligne :

1. **`isset($dispatchMap[$page])`** : la page existe-t-elle dans l'annuaire ? Si non, on saute ce bloc.
2. **`[$controller, $method] = $dispatchMap[$page];`** : c'est de la **déstructuration**.
   `$dispatchMap['connexion']` renvoie le tableau `[AuthController, 'login']`, et cette ligne
   **crée deux variables** à partir de lui : `$controller` reçoit l'élément 0 (l'objet contrôleur),
   `$method` reçoit l'élément 1 (le texte `'login'`).
   *(Équivalent long : `$controller = $dispatchMap[$page][0]; $method = $dispatchMap[$page][1];`)*
3. **`$controller->$method();`** : c'est un **appel dynamique de méthode**. Comme `$method`
   contient `'login'`, écrire `$controller->$method()` revient exactement à écrire
   `$controller->login()`. La méthode s'exécute et renvoie un **tableau** de données.
4. **`extract($viewData);`** : transforme ce tableau associatif en **variables** utilisables
   dans la vue. La clé `'message_error'` devient la variable `$message_error`.
   *(C'est l'inverse de `compact()`, que les contrôleurs utilisent pour fabriquer ce tableau.)*

---

## Étape 7 — Trouver la VUE et le TITRE (table `pages`)

```php
$pageModel    = new Page();
$page_content = $pageModel->getByMod($page);   // SELECT * FROM pages WHERE mod = 'connexion'

if (!empty($page_content['nom']) && !empty($page_content['url'])) {
    $page_title = $page_content['nom'];   // 'Connexion'
    $page_url   = $page_content['url'];   // 'views/login.php'
} else {
    $page_title = 'Page introuvable';
    $page_url   = 'views/404.php';        // slug inconnu → 404
}
```

- Le `$dispatchMap` a dit **quel code exécuter** ; la table `pages` dit **quoi afficher**.
- La méthode `getByMod()` (modèle `Page`) fait une **requête préparée** sur la table `pages`
  et renvoie le **titre** (`nom`) et le **fichier de vue** (`url`) correspondant au slug.
- Les deux mécanismes sont reliés par **la même clé** : `$page`.
- Si rien ne correspond → on bascule sur la vue **404**.

---

## Étape 8 — Choisir le layout et afficher

```php
$is_admin_page = strpos($page_url, 'views/admin/') === 0;

if ($is_admin_page) {
    include('views/partials/admin_head.php');
    include($page_url);                          // ← la vue (views/login.php)
    include('views/partials/admin_foot.php');
} else {
    include('views/partials/head.php');
    include('views/partials/header.php');
    include($page_url);                          // ← la vue
    include('views/partials/footer.php');
    include('views/partials/foot.php');
}
```

- Si le fichier de vue est dans `views/admin/` → layout **administration** (template SB Admin).
- Sinon → layout **public**.
- La vue (`$page_url`) est insérée **au milieu** de son habillage, avec le titre (`$page_title`).
- Résultat : l'en-tête et le pied de page ne sont écrits **qu'une seule fois**, et toutes
  les pages en héritent.

---

## Le schéma à mémoriser

```
/connexion
   │  .htaccess réécrit
   ▼
index.php?mod=connexion
   │  $page = 'connexion'
   ├──────────────► $dispatchMap['connexion'] → AuthController::login() → données (extract)
   │
   └──────────────► Page::getByMod('connexion') → views/login.php + titre 'Connexion'
                          │
                          ▼
              layout (head/header) + views/login.php + (footer/foot) → PAGE AFFICHÉE
```

---

## Phrase de synthèse (à dire d'un trait)

> « Toutes les URLs sont réécrites par le `.htaccess` vers un point d'entrée unique, `index.php` :
> c'est le pattern Front Controller. Là, je récupère le nom de la page dans le paramètre `mod`.
> Ma table de routage, `$dispatchMap`, associe ce nom à une paire contrôleur + méthode : je la
> déballe dans deux variables et j'appelle dynamiquement la méthode, qui me renvoie les données,
> rendues disponibles par `extract()`. Ensuite, la table `pages` me donne le fichier de vue et le
> titre pour ce même nom de page. Enfin, j'enveloppe la vue dans le bon layout, public ou admin.
> Pour ajouter une page, je n'ajoute qu'une ligne dans la table de routage. »

---
---

# ❓ Questions / Réponses du jury

**Q — C'est quoi le pattern Front Controller ?**
R — Un patron de conception où **toutes les requêtes arrivent au même endroit** (`index.php`),
qui se charge ensuite de router vers le bon traitement. Ça centralise le routage en un seul point.

**Q — À quoi sert le `.htaccess` ?**
R — Il réécrit les URLs : il transforme une adresse propre comme `/connexion` en
`index.php?mod=connexion`, sans que le visiteur le voie. Ça me donne des URLs lisibles et
meilleures pour le référencement.

**Q — Pourquoi les deux `RewriteCond` (`!-f` et `!-d`) ?**
R — Pour que les **vrais fichiers** (images, CSS, JS, favicon) soient servis directement,
sans passer par le routeur. Sans ces conditions, tout serait redirigé vers `index.php`, même mes assets.

**Q — C'est quoi `$_GET['mod']` ?**
R — `$_GET` contient les paramètres de l'URL. `$_GET['mod']` me donne la valeur du paramètre `mod`,
c'est-à-dire le nom de la page. Il est rempli par le `.htaccess`.

**Q — Et si l'URL n'a pas de `mod` ?**
R — Mon ternaire met `'accueil'` par défaut. La racine du site affiche donc la page d'accueil.

**Q — C'est quoi `$dispatchMap` exactement ?**
R — Un tableau associatif qui sert d'annuaire de routage : à chaque nom de page, j'associe une paire
(contrôleur, méthode). Ça remplace une longue suite de `if/else` ou un gros `switch`.

**Q — Pourquoi pas un `switch` ou des `if` ?**
R — Un `switch` ferait la même chose, mais avec un bloc répété pour chaque page. La table de
routage fait tenir chaque page sur **une ligne**, et un seul code les gère toutes : plus court,
plus lisible, et pour ajouter une page je n'ajoute qu'une ligne.

**Q — Explique `[$controller, $method] = $dispatchMap[$page];`**
R — C'est de la **déstructuration**. `$dispatchMap[$page]` renvoie un tableau de deux éléments ;
cette syntaxe crée deux variables et y range le premier (le contrôleur) et le second (le nom de la méthode).

**Q — Comment `$controller->$method()` peut marcher si le nom est une variable ?**
R — C'est un **appel dynamique** : comme `$method` contient le texte `'login'`,
`$controller->$method()` revient à `$controller->login()`. PHP remplace la variable par son contenu.

**Q — À quoi sert `extract()` ?**
R — Le contrôleur renvoie un tableau associatif (fabriqué avec `compact()`). `extract()` le
transforme en variables directement utilisables dans la vue : la clé `message_error` devient `$message_error`.
Ça m'évite d'écrire `$viewData['...']` partout dans le HTML.

**Q — D'où viennent le titre et le fichier de vue ?**
R — De la table `pages` en base. Le modèle `Page` (méthode `getByMod`) renvoie, pour le slug
demandé, le titre (`nom`) et le chemin de la vue (`url`). C'est complémentaire du `$dispatchMap` :
l'un dit quoi exécuter, l'autre quoi afficher.

**Q — Quel est le lien entre `$dispatchMap` et la table `pages` ?**
R — Les deux travaillent sur **la même clé**, le slug `$page`. Le `$dispatchMap` (en PHP) donne
le contrôleur + la méthode pour préparer les **données** ; la table `pages` (en BDD) donne le
**fichier de vue** + le **titre**. Le slug est le fil conducteur entre la logique et l'affichage.

**Q — Que se passe-t-il si la page n'existe pas ?**
R — Si le slug n'est ni dans `$dispatchMap` ni dans la table `pages`, je retombe sur la vue 404.

**Q — Comment choisis-tu entre le layout public et admin ?**
R — Je regarde si le chemin de la vue commence par `views/admin/` (avec `strpos(...) === 0`).
Si oui, layout administration ; sinon, layout public.

**Q — `$_GET['mod']` vient de l'utilisateur, c'est risqué ?**
R — Non, car je m'en sers seulement comme **clé de recherche** dans mon annuaire et dans une
**requête préparée** sur la table `pages`. Si la valeur ne correspond à rien, il ne se passe rien
(404). Je ne l'utilise jamais directement dans une requête SQL concaténée ni dans un `include` non contrôlé.

**Q — Pourquoi instancier tous les contrôleurs dans `$dispatchMap` ?**
R — C'est un point d'amélioration honnête : aujourd'hui, écrire `new XController()` dans chaque
ligne crée tous les contrôleurs à chaque requête, alors qu'un seul sert. Ça reste léger (leurs
constructeurs ne font rien), mais je pourrais n'instancier que le contrôleur retenu une fois la
page connue. C'est un axe d'optimisation que j'ai identifié.

**Q — Comment ajoutes-tu une nouvelle page ?**
R — J'ajoute une ligne dans `$dispatchMap` (slug → contrôleur + méthode), et une entrée dans la
table `pages` (slug → vue + titre). La nouvelle URL est alors routée.

**Q — Axe d'évolution possible sur ce routage ?**
R — Je pourrais fusionner la table `pages` dans ma table de routage PHP (y mettre aussi la vue et
le titre), pour éviter une requête en base et n'instancier que le contrôleur utile. Ça simplifierait
le système en gardant le même principe.
