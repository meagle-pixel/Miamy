# Fiche oral — Le routage : Front Controller et table de routage (`$dispatchMap`)

> Format **Question (jury) / Réponse (ce que je dois dire)**.
> Toutes les réponses ci-dessous ont été vérifiées dans le code réel du projet (`index.php`, `.htaccess`).
> Objectif de cette fiche : savoir expliquer **comment une URL arrive jusqu'à la bonne page**.

---

## 0. L'image à garder en tête

`index.php` est l'**accueil unique** d'un immeuble. Peu importe la page demandée, tout le monde passe par cet accueil, qui consulte un **annuaire** (`$dispatchMap`) pour rediriger vers le bon bureau (le contrôleur) et la bonne personne (la méthode).

**Le trajet complet :**

```
URL tapée → .htaccess → index.php → $dispatchMap → Contrôleur → (Modèle/BDD) → données → Vue → Layout → page affichée
```

---

## 1. Le point d'entrée unique (Front Controller)

**Q — Comment ton application gère-t-elle les différentes pages ?**

R — J'utilise le pattern **Front Controller** : toutes les requêtes passent par un **seul fichier d'entrée**, `index.php`. C'est lui qui décide quelle page afficher. Je n'ai pas un fichier PHP par page ; j'ai un point d'entrée unique qui répartit le travail.

---

**Q — Qu'est-ce que le pattern Front Controller, en une phrase ?**

R — C'est un patron de conception où **toutes les requêtes HTTP arrivent au même endroit**, qui se charge ensuite de les router vers le bon traitement. Ça centralise le routage en un seul point au lieu de l'éparpiller.

---

**Q — Quel intérêt par rapport à un fichier par page ?**

R — Tout ce qui est commun (démarrage de session, chargement de la config et des classes, choix du layout) est écrit **une seule fois** dans `index.php`, au lieu d'être répété dans chaque page. C'est plus simple à maintenir et ça évite les oublis.

---

## 2. La réécriture d'URL (`.htaccess`)

**Q — Comment passe-t-on de l'URL propre `/liste-restaurants` à ton `index.php` ?**

R — Grâce à un fichier `.htaccess` à la racine, qui réécrit l'URL. La règle est :

```apache
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^([a-zA-Z0-9\-_]+)/?$ index.php?mod=$1 [L,QSA]
```

En français : « si l'URL demandée ne correspond **pas** à un vrai fichier (`!-f`) ni à un vrai dossier (`!-d`) existant, alors envoie la demande vers `index.php` en plaçant le nom de la page dans un paramètre `mod`. »

Donc `/liste-restaurants` devient en interne `index.php?mod=liste-restaurants`. **L'utilisateur ne voit pas cette réécriture** : son URL reste propre dans la barre d'adresse.

---

**Q — Pourquoi des URL propres ? Quel intérêt ?**

R — Elles sont plus lisibles et mémorisables pour l'utilisateur, et meilleures pour le référencement (SEO) que des URL du type `index.php?mod=...`.

---

## 3. Le tableau associatif (la notion de base)

**Q — C'est quoi un tableau associatif ?**

R — C'est une liste **à étiquettes** : à chaque **clé** (à gauche) correspond une **valeur** (à droite). On récupère une valeur en donnant sa clé. Exemple simple :

```php
$repertoire = [
    'Maxime' => '0612345678',
    'Yann'   => '0698765432',
];
echo $repertoire['Yann']; // affiche 0698765432
```

Je donne la clé `'Yann'`, PHP me rend la valeur associée. C'est exactement le mécanisme que j'utilise pour le routage.

---

## 4. La structure de `$dispatchMap`

**Q — Montre et explique ta table de routage.**

R — C'est un tableau associatif où **la clé est le nom de la page**, et **la valeur est une paire** : le contrôleur à utiliser + la méthode à appeler.

```php
$dispatchMap = [
    'accueil'           => [new HomeController(),       'index'],
    'liste-restaurants' => [new RestaurantController(), 'liste'],
    'gestion-carte'     => [new PlatController(),       'gestionCarte'],
    'connexion'         => [new AuthController(),       'login'],
    // ... une ligne par page
];
```

Pour la clé `'gestion-carte'`, la valeur est `[new PlatController(), 'gestionCarte']`, c'est-à-dire **deux choses** :

1. `new PlatController()` → un objet du contrôleur des plats (le « bureau »).
2. `'gestionCarte'` → le nom de la méthode à appeler dans ce contrôleur (l'« action »).

Les crochets `[...]` autour des deux éléments créent cette petite liste de deux cases.

---

## 5. Récupérer la page demandée

**Q — Comment sais-tu quelle page a été demandée ?**

R — Je lis le paramètre `mod` posé par `.htaccess`. S'il n'y en a pas, je considère que c'est la page d'accueil :

```php
$page = isset($_GET['mod']) ? $_GET['mod'] : 'accueil';
```

Pour `/liste-restaurants`, `$page` vaut `'liste-restaurants'`.

---

## 6. Déballer la paire (contrôleur + méthode)

**Q — Explique ces deux lignes :**

```php
[$controller, $method] = $dispatchMap[$page];
$viewData = $controller->$method();
```

R — La première ligne fait deux choses :

1. `$dispatchMap[$page]` va chercher la valeur associée à la page (comme `$repertoire['Yann']` rendait un numéro). Ça renvoie la paire `[new RestaurantController(), 'liste']`.
2. `[$controller, $method] = ...` **range cette paire dans deux variables** : le premier élément (l'objet contrôleur) va dans `$controller`, le deuxième (le nom de la méthode) va dans `$method`. On appelle ça « déballer » la paire.

Après cette ligne :
- `$controller` = un objet `RestaurantController`
- `$method` = le texte `'liste'`

---

## 7. L'appel dynamique de méthode

**Q — Comment `$controller->$method()` peut-il fonctionner alors que le nom de la méthode est une variable ?**

R — En PHP, on peut appeler une méthode dont le nom est **stocké dans une variable**. Comme `$method` contient `'liste'`, écrire `$controller->$method()` revient exactement à écrire `$controller->liste()`. Cette ligne **exécute** donc la méthode du contrôleur et range ce qu'elle renvoie dans `$viewData`.

Le contrôleur, lui, va chercher les données en base (via les modèles) et **retourne un tableau** de variables destinées à la vue.

---

## 8. `extract()` : du tableau aux variables

**Q — À quoi sert `extract()` ?**

```php
if (is_array($viewData)) {
    extract($viewData);
}
```

R — Le contrôleur renvoie un tableau associatif, par exemple `['restos' => [...], 'titre' => '...']`. `extract()` transforme ce tableau en **variables directement utilisables dans la vue** : la clé `'restos'` devient la variable `$restos`. Ça évite d'écrire `$viewData['restos']` partout dans le HTML ; j'écris simplement `$restos`.

---

## 9. Pourquoi ce système plutôt que des `if` ?

**Q — Pourquoi une table de routage plutôt qu'une suite de `if / elseif` ?**

R — Sans table de routage, il faudrait écrire un `if` par page :

```php
if ($page == 'gestion-carte') {
    $controller = new PlatController();
    $viewData = $controller->gestionCarte();
} elseif ($page == 'liste-restaurants') {
    $controller = new RestaurantController();
    $viewData = $controller->liste();
} elseif (...) // pour CHAQUE page
```

Ça ferait des dizaines de `if`, lourds et illisibles. Avec `$dispatchMap`, **toutes les pages tiennent dans un seul annuaire**, et le même petit bloc de code les gère toutes. C'est plus court, plus clair, et centralisé.

---

**Q — Comment ajoutes-tu une nouvelle page à ton site ?**

R — J'ajoute **une seule ligne** dans `$dispatchMap` : la clé (le nom de l'URL) et la paire (contrôleur, méthode). C'est tout : la nouvelle URL est immédiatement branchée. C'est l'un des gros avantages du système.

---

## 10. Le choix du layout (juste après le routage)

**Q — Une fois la méthode du contrôleur appelée, comment la page est-elle affichée ?**

R — `index.php` détermine le fichier de vue et son titre (via le modèle `Page`), puis l'**enveloppe dans le bon habillage** : layout administration si la vue est dans `views/admin/`, sinon layout public.

```php
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

Ainsi, l'en-tête et le pied de page ne sont écrits **qu'une seule fois**, et toutes les pages en héritent.

---

## 11. Questions pièges possibles

**Q — Que se passe-t-il si l'URL demandée n'existe pas dans `$dispatchMap` ?**

R — Le bloc de dispatch ne s'exécute pas (le `if (isset($dispatchMap[$page]))` est faux). Ensuite, le modèle `Page` ne trouve pas de vue correspondante, et je retombe sur une page « introuvable » (vue 404).

---

**Q — Pourquoi instancies-tu tous les contrôleurs dans `$dispatchMap`, même ceux qu'on n'utilisera pas pour cette requête ?**

R — C'est un point d'amélioration honnête : aujourd'hui, écrire `new PlatController()` dans chaque ligne crée tous les contrôleurs à chaque requête, alors qu'un seul sert. Ça reste léger ici (les contrôleurs ne font rien dans leur constructeur), mais on pourrait n'instancier que le contrôleur retenu, une fois la page connue. C'est un axe d'optimisation possible.

---

**Q — `$_GET['mod']`, ça ne vient pas de l'utilisateur ? C'est un risque ?**

R — `mod` est rempli par `.htaccess`, mais comme tout paramètre, il pourrait être manipulé. Ce n'est pas un risque ici car je ne fais que **chercher cette valeur comme clé dans mon tableau** : si elle ne correspond à aucune entrée, il ne se passe rien (page 404). Je n'utilise jamais `mod` directement dans une requête SQL ou un `include` non contrôlé.

---

## 12. Phrase de synthèse (à dire d'un trait)

> « Toutes les URLs sont réécrites par `.htaccess` vers un point d'entrée unique, `index.php` : c'est le pattern Front Controller. Là, une table de routage associative associe le nom de la page à une paire contrôleur + méthode. Je déballe cette paire dans deux variables et j'appelle la méthode du contrôleur, qui me renvoie les données. `extract()` les transforme en variables, et `index.php` enveloppe la vue dans le bon layout, public ou admin. Pour ajouter une page, je n'ajoute qu'une ligne dans la table de routage. »
