# APP_URL — La constante de configuration des URLs du projet Miamy

## Chapitre 1 — Le problème qu'APP_URL résout

Imagine que tu veux écrire un lien vers ta page de connexion. Tu écris dans ton HTML :

```html
<a href="/connexion">Se connecter</a>
```

Le `/` au début veut dire "à partir de la racine du serveur". Donc :

- **En production** sur `miamy.fr` : le navigateur va sur `https://miamy.fr/connexion` ✓
- **En local** sur ton XAMPP/WAMP où Miamy est dans un sous-dossier (`http://localhost/Miamy/`) : le navigateur va sur `http://localhost/connexion` qui n'existe pas → erreur 404 ✗

C'est LE problème classique de tout projet web qui doit tourner à la fois en local et en prod. Sans solution, tu passes ton temps à modifier des URLs avant chaque déploiement, et tu fais inévitablement des erreurs.

## Chapitre 2 — La solution : une constante qui contient l'URL de base

L'idée est simple : on définit **une seule fois** la "racine" de l'application, et on l'utilise partout.

```php
// En local
APP_URL = 'http://localhost/Miamy'

// En prod
APP_URL = 'https://miamy.fr'
```

Et au lieu d'écrire `/connexion` en dur, on écrit `APP_URL . '/connexion'`. Le PHP concatène les deux :

- En local : `'http://localhost/Miamy' . '/connexion'` → `http://localhost/Miamy/connexion` ✓
- En prod : `'https://miamy.fr' . '/connexion'` → `https://miamy.fr/connexion` ✓

**Une seule ligne de code à changer** entre local et prod (la valeur d'APP_URL), et tout le projet s'adapte automatiquement.

## Chapitre 3 — Comment c'est défini dans Miamy

Tout se passe dans `config.php`. Voici les lignes clés :

```php
// Détection : suis-je en local ou en prod ?
$isLocal = ($_SERVER['REMOTE_ADDR'] == '127.0.0.1' || $_SERVER['HTTP_HOST'] == 'localhost');

if ($isLocal) {
    // CONFIGURATION LOCALHOST (Docker)
    define('APP_URL', $_ENV['DEV_URL'] ?? 'http://localhost/Miamy');
    define('APP_DEV', true);
    // ... autres constantes DB
} else {
    // CONFIGURATION PRODUCTION (o2switch)
    define('APP_URL', $_ENV['PROD_URL'] ?? 'https://miamy.fr');
    define('APP_DEV', false);
    // ...
}
```

Décortiquons :

**`$_SERVER['REMOTE_ADDR']` et `$_SERVER['HTTP_HOST']`** sont des variables globales que PHP remplit automatiquement à chaque requête. Elles contiennent l'IP du visiteur et le nom de domaine demandé. Si on voit `127.0.0.1` (= localhost, "moi-même") ou que le domaine est littéralement `localhost`, on sait qu'on est en local.

**`define('APP_URL', '...')`** crée une **constante PHP**. Une constante, c'est comme une variable, sauf que sa valeur ne peut **jamais** être changée par la suite. Tu ne peux pas faire `APP_URL = 'autre chose'` plus tard — PHP planterait.

**`$_ENV['DEV_URL'] ?? 'http://localhost/Miamy'`** veut dire "si la variable d'environnement `DEV_URL` est définie (dans le fichier `.env`), prendre sa valeur ; sinon, prendre `http://localhost/Miamy` par défaut". Ça permet de personnaliser sans toucher au code.

## Chapitre 4 — Pourquoi `define()` plutôt qu'une variable ?

C'est une question importante. Une **variable** se réécrit librement :

```php
$url = 'http://localhost/Miamy';
$url = 'piraté'; // ← aucun problème, PHP accepte
```

Une **constante** est protégée contre les modifications :

```php
define('APP_URL', 'http://localhost/Miamy');
define('APP_URL', 'autre chose'); // ← PHP refuse : "Constant already defined"
```

C'est un **gage de sécurité et de stabilité** : une fois définie, APP_URL est garantie immuable pour toute la durée de la requête. Aucun bug imprévu, aucun script malicieux ne peut la changer.

## Chapitre 5 — Comment APP_URL est utilisée dans le code

On la trouve à 3 endroits principaux dans Miamy.

### 5.1 — Dans les contrôleurs (pour les redirections PHP)

```php
public function logout()
{
    // ... destruction de session ...
    header('Location: ' . APP_URL . '/accueil');
    exit();
}
```

`header('Location: ...')` envoie au navigateur un en-tête HTTP qui dit "va à cette URL". On compose l'URL complète avec `APP_URL . '/accueil'` :

- En local → `http://localhost/Miamy/accueil`
- En prod → `https://miamy.fr/accueil`

Pareil dans les méthodes AJAX (en cas d'erreur), dans les vérifications de session (`header('Location: ' . APP_URL . '/connexion')`), partout où on redirige.

### 5.2 — Dans les vues (pour les ressources statiques)

Pour afficher une image :

```html
<img src="<?= APP_URL ?>/assets/img/restaurants/<?= $resto['main_image'] ?>" />
```

Le `<?= APP_URL ?>` est PHP qui dit "écris la valeur de APP_URL ici". Le navigateur reçoit donc le HTML déjà résolu :

```html
<img src="http://localhost/Miamy/assets/img/restaurants/bistrot.jpg" />
```

Pareil pour les CSS, JS, polices :

```html
<link rel="stylesheet" href="<?= APP_URL ?>/assets/css/fontawesome.all.min.css" />
<script src="<?= APP_URL ?>/assets/js/custom.js"></script>
```

### 5.3 — Dans les liens HTML

```html
<a href="<?= APP_URL ?>/admin-panel">Gestion des utilisateurs</a>
```

À chaque fois qu'on a besoin d'une URL absolue dans le HTML, on utilise APP_URL.

## Chapitre 6 — L'astuce de la balise `<base>` qui simplifie tout

Tu remarqueras qu'on n'écrit PAS `<a href="<?= APP_URL ?>/connexion">` dans toutes les vues — on écrit juste `<a href="connexion">`. Pourquoi ça marche ?

Parce que dans `views/partials/head.php`, on a placé une balise HTML5 magique :

```html
<head>
    <base href="<?= APP_URL ?>/" />
    ...
</head>
```

**La balise `<base>`** dit au navigateur : "pour tous les liens relatifs de cette page, considère que la racine est cette URL". Du coup :

- Tu écris `<a href="connexion">` (relatif, sans `/` devant)
- Le navigateur lit `<base href="http://localhost/Miamy/">` et résout `connexion` en `http://localhost/Miamy/connexion`

C'est PUREMENT côté HTML/navigateur. PHP n'intervient pas. C'est élégant parce que ça évite d'écrire `APP_URL` dans tous les `<a>` de toutes les vues.

**MAIS attention** : la balise `<base>` ne fonctionne QUE pour le HTML rendu (les liens, images, scripts). Elle ne fonctionne PAS pour :

- Les redirections PHP `header('Location: ...')` — il faut APP_URL
- Les redirections JavaScript `window.location.href = ...` — il faut APP_URL
- Les requêtes AJAX `fetch(...)` — il faut APP_URL

C'est pour ça qu'on garde la règle : **toujours APP_URL pour les redirections PHP/JS**.
