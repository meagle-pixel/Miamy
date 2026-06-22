# Fiche oral — Leçons 1 & 2

> Tout expliqué en langage simple, avec des images pour retenir.
> À la fin de chaque leçon : les questions probables du jury + les réponses à donner.
> Tout a été vérifié dans le code réel du projet.

---
---

# LEÇON 1 — Le cycle d'une requête

**La question à laquelle cette leçon répond :** que se passe-t-il, étape par étape, entre le moment où un visiteur tape une adresse et le moment où la page s'affiche ?

## L'image à garder en tête

`index.php` = **l'accueil unique d'un immeuble**. Peu importe la page demandée, **tout le monde passe par cet accueil**, qui consulte un annuaire et envoie chaque visiteur au bon bureau. Ça, c'est le pattern **Front Controller** : un seul point d'entrée.

Le trajet complet :

```
URL tapée → .htaccess → index.php → annuaire (dispatchMap) → Contrôleur → (Modèle/BDD) → données → Vue → Layout → page affichée
```

On suit un exemple concret tout du long : le visiteur tape `/liste-restaurants`.

---

## Étape 1 — `.htaccess` aiguille vers `index.php`

Le navigateur demande `/liste-restaurants`. Le fichier `.htaccess` (à la racine) intercepte et réécrit l'adresse :

```apache
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^([a-zA-Z0-9\-_]+)/?$ index.php?mod=$1 [L,QSA]
```

**En français :** « si l'adresse demandée ne correspond pas à un vrai fichier (`!-f`) ni à un vrai dossier (`!-d`) existant, alors envoie tout vers `index.php`, en mettant le nom de la page dans un paramètre `mod`. »

Donc `/liste-restaurants` devient en interne `index.php?mod=liste-restaurants`. **Le visiteur ne voit pas ce changement** : dans sa barre d'adresse, l'URL reste propre. C'est bon pour la lisibilité et pour le référencement (SEO).

---

## Étape 2 — `index.php` démarre tout

Tout arrive sur `index.php`, le point d'entrée unique. Ses premières lignes chargent le fichier d'amorçage :

```php
ob_start();
include('functions.php');
```

---

## Étape 3 — `functions.php` prépare le terrain

Avant de router quoi que ce soit, ce fichier fait 4 choses dans l'ordre :

```php
session_start();                           // 1. active la mémoire de session ($_SESSION)
date_default_timezone_set('Europe/Paris'); // 2. règle le fuseau horaire français
require_once('config.php');                // 3. charge la configuration
require_once('classes/class.users.php');   // 4. charge TOUTES les classes du projet
require_once('classes/class.plats.php');
// ... etc
```

**Point important à comprendre :** `require_once` ne fait que **charger les fichiers de classes** pour que PHP sache qu'elles existent. Ça ne crée **aucun objet**. C'est comme **poser tous les livres de recettes sur le plan de travail** : les recettes sont disponibles, mais on n'a encore rien cuisiné.

---

## Étape 4 — `config.php` : où tourne-t-on ?

```php
$isLocal = ($_SERVER['REMOTE_ADDR'] == '127.0.0.1' || $_SERVER['HTTP_HOST'] == 'localhost');

if ($isLocal) {
    define('DB_HOST', ...);                    // identifiants de la base Docker (local)
    define('APP_URL', 'http://localhost/Miamy');
} else {
    define('DB_HOST', ...);                    // identifiants de la base o2switch (prod)
    define('APP_URL', 'https://...');
}
```

Ce fichier **détecte tout seul** s'il tourne sur ma machine (local) ou sur le serveur en ligne (production), et il charge les bons identifiants de base de données et la bonne adresse. Résultat : **je n'ai jamais à modifier le code** entre mon poste et le serveur.

---

## Étape 5 — l'annuaire de routage (`$dispatchMap`)

De retour dans `index.php`, le cœur du système :

```php
$dispatchMap = [
    'accueil'           => [new HomeController(),       'index'],
    'liste-restaurants' => [new RestaurantController(), 'liste'],
    'gestion-carte'     => [new PlatController(),       'gestionCarte'],
    // ... une ligne par page
];

$page = isset($_GET['mod']) ? $_GET['mod'] : 'accueil';
```

### C'est quoi ce tableau ?

`$dispatchMap` est un **tableau associatif** : une liste à étiquettes. À chaque **clé** (à gauche) correspond une **valeur** (à droite). Comme un répertoire téléphonique : je donne un nom, j'obtiens un numéro.

Ici :
- **la clé** = le nom de la page (`'liste-restaurants'`)
- **la valeur** = une **paire** : `[new RestaurantController(), 'liste']`, c'est-à-dire **quel contrôleur** utiliser, et **quelle méthode** (fonction) appeler dedans.

Image : `$dispatchMap`, c'est le **tableau d'accueil de l'immeuble**. Je demande « liste-restaurants », il répond « bureau RestaurantController, demande la fonction liste ».

### Comment on s'en sert

```php
if (isset($dispatchMap[$page])) {
    [$controller, $method] = $dispatchMap[$page];   // on récupère et on "déballe" la paire
    $viewData = $controller->$method();             // on appelle la méthode du contrôleur
    if (is_array($viewData)) {
        extract($viewData);                          // on transforme le tableau en variables
    }
}
```

- `$dispatchMap[$page]` va chercher la paire associée à la page (comme `$repertoire['Yann']` rendait un numéro).
- `[$controller, $method] = ...` **range les deux éléments de la paire dans deux variables** : le contrôleur dans `$controller`, le nom de la méthode dans `$method`. On appelle ça « déballer ».
- `$controller->$method()` **exécute** la méthode. Comme `$method` contient `'liste'`, ça revient à écrire `$controller->liste()`. Le contrôleur va chercher les données en base et **retourne un tableau**.

### Petit rappel : `require` ≠ `new`

- `require_once` (étape 3) = **charger** la recette (la rendre disponible). Aucun objet créé.
- `new` (ici) = **cuisiner** le plat à partir de la recette = créer un objet réel et utilisable.

On crée chaque objet **au moment où on en a besoin** : le contrôleur ici dans le routage, et les modèles (`new Plat()`, `new User()`…) plus loin, à l'intérieur des contrôleurs.

---

## Étape 6 — `extract()` : du tableau aux variables

Le contrôleur renvoie un tableau, par exemple `['restos' => [...]]`. `extract()` le transforme en **variables utilisables dans la page** : la clé `'restos'` devient la variable `$restos`. Ça évite d'écrire `$viewData['restos']` partout dans le HTML.

---

## Étape 7 — choisir l'habillage (layout) et afficher

```php
$is_admin_page = strpos($page_url, 'views/admin/') === 0;

if ($is_admin_page) {
    include('views/partials/admin_head.php');   // habillage back-office
    include($page_url);
    include('views/partials/admin_foot.php');
} else {
    include('views/partials/head.php');         // habillage public
    include('views/partials/header.php');
    include($page_url);                          // LA page demandée
    include('views/partials/footer.php');
    include('views/partials/foot.php');
}
```

On regarde si la vue est rangée dans `views/admin/`. Si oui → habillage **administration**. Sinon → habillage **public** (en-tête + pied de page classiques). Au milieu, on insère la page demandée. C'est le **système de layouts** : l'en-tête et le pied de page ne sont écrits **qu'une seule fois**, et toutes les pages en héritent.

---

## Le schéma à mémoriser

```
URL → .htaccess → index.php → dispatchMap → Contrôleur → (Modèle/BDD) → données → Vue → Layout → page
```

## Phrase de synthèse (à dire d'un trait)

> « Toutes les URLs sont réécrites par `.htaccess` vers un point d'entrée unique, `index.php` : c'est le pattern Front Controller. Là, un annuaire associe chaque page à un contrôleur et une méthode. J'appelle cette méthode, qui me renvoie les données, et `index.php` enveloppe la vue dans le bon layout, public ou admin. Pour ajouter une page, je n'ajoute qu'une ligne dans l'annuaire. »

---

## ❓ Questions / Réponses jury — Leçon 1

**Q — C'est quoi le pattern Front Controller ?**
R — Un patron de conception où **toutes les requêtes arrivent au même endroit** (`index.php`), qui se charge ensuite de router vers le bon traitement. Ça centralise le routage en un seul point.

**Q — À quoi sert le `.htaccess` ?**
R — Il réécrit les URLs : il transforme une adresse propre comme `/liste-restaurants` en `index.php?mod=liste-restaurants`, sans que le visiteur le voie. Ça me donne des URLs lisibles et meilleures pour le référencement.

**Q — C'est quoi `$dispatchMap` ?**
R — C'est un tableau associatif qui sert d'annuaire de routage : à chaque nom de page, j'associe le contrôleur et la méthode à appeler. Ça remplace une longue suite de `if/else`.

**Q — Comment ajoutes-tu une nouvelle page ?**
R — J'ajoute une seule ligne dans `$dispatchMap` (le nom de l'URL + le contrôleur et la méthode). C'est tout.

**Q — Quelle est la différence entre `require` et `new` ?**
R — `require` charge un fichier de classe pour que PHP la connaisse. `new` crée un objet réel à partir de cette classe, quand j'en ai besoin.

**Q — Que se passe-t-il si l'URL n'existe pas ?**
R — Le routage ne trouve rien, et je retombe sur une page « introuvable » (vue 404).

**Q — `$_GET['mod']` vient en partie de l'utilisateur, c'est risqué ?**
R — Non, car je m'en sers seulement comme clé pour chercher dans mon annuaire. Si la valeur ne correspond à rien, il ne se passe rien. Je ne l'utilise jamais directement dans une requête SQL.

---
---

# LEÇON 2 — La base de données

**La question à laquelle cette leçon répond :** comment mon site parle-t-il à la base de données, et comment je le fais de manière sécurisée ?

## L'image à garder en tête

La base de données = une **salle des archives**. Pour y entrer, il faut une **clé** (la connexion). Et pour ne pas se faire piéger en posant des questions, on utilise un **formulaire à trous** (les requêtes préparées).

---

## Morceau 1 — Une seule connexion, centralisée (la classe `Database`)

Pour parler à la base, il faut d'abord **s'y connecter** (comme avec un identifiant/mot de passe). Ce travail est rangé dans **une seule classe**, `Database` (`classes/class.database.php`). C'est elle, et elle seule, qui ouvre la porte.

Partout dans le code, quand j'ai besoin de la base, j'écris :

```php
$pdo = Database::getInstance()->getConnection();
```

Ce qui veut dire : « donne-moi la connexion à la base ». Une fois que j'ai `$pdo`, je peux poser des questions à la base.

Image : **une seule clé** de la salle des archives, que tous les employés se passent, au lieu que chacun fasse refaire la sienne.

---

## Morceau 2 — PDO

**PDO**, c'est simplement l'outil officiel de PHP pour dialoguer avec une base MySQL. Quand on parle de « la connexion », c'est un objet PDO. C'est aussi PDO qui gère les requêtes préparées (voir plus bas). Rien d'autre à retenir : **PDO = le moyen standard de PHP pour parler à la base.**

---

## Morceau 3 — Le Singleton (une seule connexion partagée)

Le mot fait peur, mais l'idée est simple : **on s'arrange pour qu'il n'y ait qu'UNE seule connexion à la base dans toute la page**, partagée par tout le monde.

**Pourquoi ?** Parce qu'ouvrir une connexion coûte des ressources au serveur. Si chaque modèle (`Plat`, `User`, `Restaurant`…) ouvrait la sienne, on en aurait dix ouvertes pour rien. Avec le Singleton, on en ouvre **une**, réutilisée partout.

Comment c'est fait dans `Database` :

```php
public static function getInstance(): Database
{
    if (!self::$_instance) {            // si la connexion n'existe pas encore...
        self::$_instance = new self();  // ...je la crée (UNE seule fois)
    }
    return self::$_instance;            // sinon, je renvoie celle qui existe déjà
}
```

**En français :** « la première fois qu'on me demande la connexion, je la fabrique. Toutes les fois suivantes, je renvoie la même. »

Deux protections complètent le mécanisme :
- Le **constructeur est privé** → impossible de faire `new Database()` ailleurs. On est obligé de passer par `getInstance()`.
- La méthode **`__clone()` est privée** → impossible de cloner l'objet pour en avoir un deuxième.

Image : la **clé unique** des archives, fabriquée la première fois qu'on en a besoin, puis prêtée à tous.

---

## Morceau 4 — Les requêtes préparées (LE point sécurité)

### Le problème : l'injection SQL

Quand le site interroge la base, il écrit une phrase en SQL, par exemple :

```sql
SELECT * FROM utilisateurs WHERE email = 'ce-que-tape-le-visiteur'
```

Si on colle **directement** ce que tape le visiteur dans cette phrase, un pirate peut taper quelque chose de tordu qui **change le sens de la commande** (pour se connecter sans mot de passe, lire des données interdites…). C'est l'**injection SQL**. Le danger vient du fait qu'on **mélange** la structure de la commande et la valeur tapée par l'utilisateur.

### La solution : séparer la commande et la valeur

On procède en **deux temps** :

```php
// 1. Je prépare la commande avec un TROU (:email), sans la valeur :
$stmt = $this->pdo->prepare("SELECT * FROM utilisateurs WHERE email = :email");

// 2. J'envoie la valeur séparément, pour remplir le trou :
$stmt->execute(['email' => $email]);
```

À l'étape 1, j'envoie à MySQL la **structure** de la commande, avec le trou vide : la forme est figée. À l'étape 2, j'envoie la **valeur** à part. Et là le point clé : MySQL traite cette valeur comme **du simple texte, jamais comme du code SQL**. Donc même si un pirate tape une commande piégée dans le champ email, elle est prise pour un bête bout de texte et ne peut plus modifier la requête.

Image : un **formulaire à trous** déjà imprimé. La structure (« Nom : ___ ») est figée ; le visiteur ne peut que **remplir le trou**, jamais réécrire le formulaire.

J'utilise des requêtes préparées **partout** dans mes modèles : c'est cohérent dans tout le projet.

---

## Phrase de synthèse (à dire d'un trait)

> « Toute la communication avec la base passe par une seule connexion partagée — le Singleton — via PDO. Et chaque requête est préparée : je sépare la commande des valeurs, ce qui rend les injections SQL impossibles. »

---

## ❓ Questions / Réponses jury — Leçon 2

**Q — Comment ton site se connecte-t-il à la base ?**
R — Via PDO, l'outil standard de PHP. Toute la connexion est centralisée dans une seule classe, `Database`. Aucune autre partie du code n'ouvre de connexion.

**Q — Pourquoi PDO ?**
R — C'est l'extension officielle et moderne de PHP. Elle gère les requêtes préparées (protection contre les injections SQL) et elle est portable d'une base à l'autre.

**Q — C'est quoi le pattern Singleton ?**
R — Un patron qui garantit qu'une classe n'a qu'**une seule instance**. Je l'utilise pour avoir une seule connexion à la base, partagée par toute la page, au lieu d'en ouvrir une nouvelle à chaque requête.

**Q — Pourquoi le constructeur de `Database` est-il privé ?**
R — Pour empêcher de faire `new Database()` ailleurs et d'ouvrir plusieurs connexions par erreur. Le seul moyen d'obtenir l'objet est `getInstance()`, qui garantit l'instance unique.

**Q — À quoi sert `__clone()` privée ?**
R — À empêcher de cloner l'objet, ce qui créerait une deuxième connexion et casserait le principe d'instance unique.

**Q — C'est quoi une injection SQL, et comment tu t'en protèges ?**
R — C'est quand un pirate insère du code SQL dans un champ pour détourner une requête. Je m'en protège avec les **requêtes préparées** : je prépare la commande avec des trous, puis j'envoie les valeurs séparément. MySQL les traite comme du texte, jamais comme du code.

**Q — Où sont stockés tes identifiants de base de données ?**
R — Pas en dur dans le code : dans un fichier `.env` non versionné. `config.php` les lit et détecte automatiquement si je suis en local ou en production pour charger les bons.
